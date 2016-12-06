DROP FUNCTION IF EXISTS hex_to_int(varchar) CASCADE;
CREATE OR REPLACE FUNCTION hex_to_int(hexval varchar) RETURNS integer AS $$
DECLARE
  result  int;
BEGIN
  EXECUTE 'SELECT x''' || hexval || '''::int' INTO result;
  RETURN result;
END;
$$ LANGUAGE plpgsql IMMUTABLE STRICT;


-- Contribution counts per year
DROP VIEW IF EXISTS vw_contributions_per_year CASCADE;
CREATE VIEW vw_contributions_per_year AS
  select
      to_char(cn.commited_at, 'YYYY') as date,
      cn.project_id,
      count(*) as contribution_count,
      sum(cast(c.is_core_member as int)) as core_team_contribution_count
  from contribution cn
  left join contributor c on cn.contributor_id = c.id
  group by to_char(cn.commited_at, 'YYYY'), cn.project_id
  order by date asc;

-- Contribution counts per year
DROP VIEW IF EXISTS vw_contributions_per_month CASCADE;
CREATE VIEW vw_contributions_per_month AS
  select
    to_char(cn.commited_at, 'YYYY-MM-01') as date,
    cn.project_id,
    count(*) as contribution_count,
    sum(cast(c.is_core_member as int)) as core_team_contribution_count
  from contribution cn
    left join contributor c on cn.contributor_id = c.id
  group by date, cn.project_id
  order by date asc;



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
           string_agg(project_id::text, ','::text) as project_ids,
           string_agg(project_name, ' and '::text) as project_names,
           to_hex(avg(hex_to_int(color))::int) AS color
         from (
                SELECT
                  DISTINCT cn.contributor_id, cn.project_id, p.name as project_name, p.color
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


DROP VIEW IF EXISTS vw_commit_count_distribution CASCADE;
CREATE VIEW vw_commit_count_distribution AS
  SELECT project_id, get_range_bounds_description(get_range_bounds(contribution_count, ARRAY[[1,1],[2,2],[3,5],[5,10],[10,30],[30,200],[200,null]])) as bounds, count(*) as contributor_count
  FROM
    (
      SELECT project_id, contributor_id, count(*) as contribution_count
      FROM contribution
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
  select iso, count(id) AS contributor_count from (
     select c.id, COALESCE(NULLIF(cn1.iso2, ''), NULLIF(cn2.iso2, '')) as iso
     from contributor c
       left join sensiolabs_user s on s.contributor_id = c.id
       left join country cn1 on cn1.name = c.country
       left join country cn2 on cn2.name = s.country
     where
       (c.country != '' OR s.country != '') and
       exists (
           select cn.id
           from contribution cn
           where cn.contributor_id = c.id and project_id IN (1,2)
       )) countries
  group by iso;