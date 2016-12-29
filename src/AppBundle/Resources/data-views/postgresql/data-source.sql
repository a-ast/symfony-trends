DROP FUNCTION IF EXISTS hex_to_int(varchar) CASCADE;
CREATE OR REPLACE FUNCTION hex_to_int(hexval varchar) RETURNS integer AS $$
DECLARE
  result  int;
BEGIN
  EXECUTE 'SELECT x''' || hexval || '''::int' INTO result;
  RETURN result;
END;
$$ LANGUAGE plpgsql IMMUTABLE STRICT;

--------------------------------------------------------
-- Commit counts per year
-- Series
-- * contribution_count
-- * contributor_count
-- * core_team_contribution_count
--------------------------------------------------------
DROP VIEW IF EXISTS vw_contributions_per_year CASCADE;
CREATE VIEW vw_contributions_per_year AS
  SELECT
      to_char(cn.commited_at, 'YYYY') AS date,
      cn.project_id,
      count(*) AS contribution_count,
      count(DISTINCT cn.contributor_id) AS contributor_count,
      sum(cast(c.is_core_member AS int)) AS core_team_contribution_count
  FROM contribution cn
    LEFT JOIN contributor c on cn.contributor_id = c.id
  WHERE is_maintenance_commit = FALSE
  GROUP BY to_char(cn.commited_at, 'YYYY'), cn.project_id
  ORDER BY date asc;


-- Contribution counts per month
DROP VIEW IF EXISTS vw_contributions_per_month CASCADE;
CREATE VIEW vw_contributions_per_month AS
  SELECT
      to_char(cn.commited_at, 'YYYY-MM-01') AS date,
      cn.project_id,
      count(*) AS contribution_count,
      sum(cast(c.is_core_member AS int)) AS core_team_contribution_count
  FROM contribution cn
      LEFT JOIN contributor c on cn.contributor_id = c.id
  WHERE is_maintenance_commit = FALSE
  GROUP BY date, cn.project_id
  ORDER BY date asc;



-- Intersections
DROP VIEW IF EXISTS vw_contributor_intersection CASCADE;
CREATE VIEW vw_contributor_intersection AS
  SELECT
    project_ids,
    project_names,
    COUNT(*) AS contributor_count,
    '#' || color AS color
  FROM (
         SELECT
           contributor_id,
           string_agg(project_id::text, ','::text) AS project_ids,
           string_agg(project_name, ' and '::text) AS project_names,
           to_hex(avg(hex_to_int(color))::int) AS color
         FROM (
                SELECT 
                    DISTINCT cn.contributor_id, cn.project_id, p.name AS project_name, p.color
                FROM contribution cn
                    LEFT JOIN project p ON p.id = cn.project_id
                ORDER BY project_id ASC
              ) c
         GROUP BY contributor_id
       ) gc
  GROUP BY project_ids, project_names, color;



DROP FUNCTION IF EXISTS get_range_bounds(bigint, bigint[][]) CASCADE;
CREATE OR REPLACE FUNCTION get_range_bounds(value bigint, ranges bigint[][]) RETURNS bigint[] AS $$
DECLARE
  r bigint[];
  range_type text;
BEGIN

  FOREACH r SLICE 1 IN ARRAY ranges
  LOOP
      range_type := '[)';
      IF r[1] = r[2] THEN
          range_type := '[]';
      END IF;
  
      IF value <@ int8range(r[1], r[2], range_type) THEN
          RETURN r;
      END IF;
  END LOOP;
  
  RETURN ARRAY[NULL, NULL];

END;
$$ LANGUAGE plpgsql IMMUTABLE STRICT;


DROP FUNCTION IF EXISTS get_range_bounds_description(bigint[2]) CASCADE;
CREATE OR REPLACE FUNCTION get_range_bounds_description(bounds bigint[2]) RETURNS text AS $$
BEGIN
  IF bounds[1] IS NULL AND bounds[2] IS NULL THEN
    RETURN '';
  ELSEIF bounds[1] IS NULL THEN
      RETURN '<' || bounds[2]::text;
  ELSEIF bounds[2] IS NULL THEN
      RETURN bounds[1]::text || '+';
  ELSEIF bounds[1] = bounds[2] THEN
      RETURN bounds[1]::text;
  ELSE
      RETURN bounds[1]::text || '-' || bounds[2]::text;
  END IF;
  RETURN '';
END;
$$ LANGUAGE plpgsql IMMUTABLE STRICT;


--------------------------------------------------------
-- Commit count distribution
--------------------------------------------------------
DROP VIEW IF EXISTS vw_commit_count_distribution CASCADE;
CREATE VIEW vw_commit_count_distribution AS
  SELECT project_id, get_range_bounds_description(get_range_bounds(contribution_count, ARRAY[[1,1],[2,2],[3,5],[5,10],[10,30],[30,200],[200,null]])) AS bounds, count(*) AS contributor_count
  FROM
    (
      SELECT project_id, contributor_id, count(*) AS contribution_count
      FROM contribution
      WHERE is_maintenance_commit = FALSE
      GROUP BY project_id, contributor_id
      ORDER BY contribution_count ASC
    ) cn
  GROUP BY project_id, bounds
  ORDER BY contributor_count;


--------------------------------------------------------
-- Contributor countries
--------------------------------------------------------
DROP VIEW IF EXISTS vw_contributor_countries CASCADE;
CREATE VIEW vw_contributor_countries AS
  SELECT iso, count(id) AS contributor_count
  FROM (

     SELECT c.id, COALESCE(NULLIF(cn1.iso2, ''), NULLIF(cn2.iso2, '')) AS iso
     FROM contributor c
         LEFT JOIN sensiolabs_user s on s.contributor_id = c.id
         LEFT JOIN country cn1 on cn1.name = c.country
         LEFT JOIN country cn2 on cn2.name = s.country
     WHERE
       (c.country != '' OR s.country != '') AND
       EXISTS (
           SELECT cn.id
           FROM contribution cn
           WHERE cn.contributor_id = c.id AND project_id IN (1,2)
       )) countries

  GROUP BY iso;

--------------------------------------------------------
-- Contributor countries
-- Parameters:
-- * v_year - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_contributor_countries(int);
CREATE FUNCTION fn_contributor_countries(v_year int) RETURNS table(iso text, contributor_count bigint) AS $$
BEGIN
  RETURN query
  SELECT countries.iso, count(countries.id) AS contributor_count
  FROM (

     SELECT c.id, COALESCE(NULLIF(cn1.iso2, ''), NULLIF(cn2.iso2, '')) AS iso
     FROM contributor c
         LEFT JOIN sensiolabs_user s on s.contributor_id = c.id
         LEFT JOIN country cn1 on cn1.name = c.country
         LEFT JOIN country cn2 on cn2.name = s.country
     WHERE
         (c.country != '' OR s.country != '') AND
         EXISTS (
             SELECT cn.id
             FROM contribution cn
             WHERE
                cn.contributor_id = c.id
                AND project_id IN (1,2)
                AND (v_year IS NULL OR v_year = date_part('year', cn.commited_at))
         )) countries

  GROUP BY countries.iso;
END;
$$ LANGUAGE plpgsql;



--------------------------------------------------------
-- Maintenance commit counts per year
--------------------------------------------------------
DROP VIEW IF EXISTS vw_maintenance_contributions_per_year CASCADE;
CREATE VIEW vw_maintenance_contributions_per_year AS
  SELECT
      to_char(cn.commited_at, 'YYYY') AS date,
      cn.project_id,
      count(*) AS contribution_count,
      SUM(cn.is_maintenance_commit::int) AS maintenance_commit_count
  FROM contribution cn
  group by
      to_char(cn.commited_at, 'YYYY'),
      cn.project_id
  ORDER BY date asc;

--------------------------------------------------------
-- New contributors per year
--------------------------------------------------------
DROP VIEW IF EXISTS vw_new_contributors_per_year CASCADE;
CREATE VIEW vw_new_contributors_per_year AS
    SELECT
        fc.project_id,
        to_char(fc.min_commited_at, 'YYYY') AS date,
        count(fc.contributor_id) AS contributor_count
    FROM (
      SELECT
          cn.project_id,
          contributor_id,
          min(commited_at) AS min_commited_at
      FROM contribution cn
      WHERE is_maintenance_commit = FALSE
      GROUP BY cn.project_id, contributor_id
      ORDER BY contributor_id ASC
    ) AS fc
    GROUP BY fc.project_id, date
    ORDER BY date asc;

--------------------------------------------------------
-- Contributors with their contribution counts
-- Parameters:
-- * v_project_id - project id
-- * v_year       - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_contributor_contribution_counts(int, int);
CREATE FUNCTION fn_contributor_contribution_counts(v_project_id int, v_year int)
    RETURNS table(project_id int, name text, contribution_count bigint, is_core_member bool) AS $$
BEGIN
    RETURN query
    SELECT
        cn.project_id,
        c.name::TEXT,
        count(*) AS contribution_count,
        c.is_core_member

    FROM contribution cn
        LEFT JOIN contributor c ON cn.contributor_id = c.id
    WHERE
        is_maintenance_commit = FALSE
        AND (v_year IS NULL OR v_year = date_part('year', cn.commited_at))
        AND (v_project_id IS NULL OR v_project_id = cn.project_id)
    GROUP BY cn.project_id, c.name, c.is_core_member
    ORDER BY contribution_count DESC;
END;
$$ LANGUAGE plpgsql;


--------------------------------------------------------
-- Contributors who started contributing during the given year withcontribution counts
-- Parameters:
-- * v_project_id - project id
-- * v_year       - year of a first contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_new_contributor_contribution_counts(int, int);
CREATE FUNCTION fn_new_contributor_contribution_counts(v_project_id int, v_year int)
    RETURNS table(project_id int, name text, contribution_count bigint, is_core_member bool) AS $$
BEGIN

    RETURN query
    SELECT
        cn.project_id,
        c.name::TEXT,
        count(*) AS contribution_count,
        c.is_core_member

    FROM (
        SELECT
            cn.project_id,
            cn.contributor_id,
            cn.is_maintenance_commit,
            first_value(cn.commited_at)
                OVER (PARTITION BY cn.contributor_id ORDER BY cn.commited_at ASC) AS first_commited_at
        FROM contribution cn
        ) cn
            LEFT JOIN contributor c ON cn.contributor_id = c.id

    WHERE
        is_maintenance_commit = FALSE
        AND (v_year = date_part('year', cn.first_commited_at))
        AND (v_project_id IS NULL OR v_project_id = cn.project_id)
    GROUP BY cn.project_id, c.name, c.is_core_member
    ORDER BY contribution_count DESC, c.name;

END;
$$ LANGUAGE plpgsql;
