--------------------------------------------------------
-- Pull request state
-- Parameters:
-- * v_merged_at
-- * v_closed_at
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_pull_request_state(timestamp, timestamp) CASCADE;
CREATE OR REPLACE FUNCTION fn_pull_request_state(v_merged_at timestamp, v_closed_at timestamp) RETURNS text AS $$
DECLARE
  result  TEXT;
BEGIN

  result := CASE
      WHEN v_merged_at IS NOT NULL THEN 'merged'
      WHEN v_merged_at IS NULL AND v_closed_at IS NOT NULL THEN 'closed'
      ELSE 'open'
  END;

	RETURN result;
END;
$$ LANGUAGE plpgsql IMMUTABLE;


--------------------------------------------------------
-- Get day count from interval
-- Parameters:
-- * v_interval
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_interval_to_days(interval) CASCADE;
CREATE OR REPLACE FUNCTION fn_interval_to_days(v_interval interval) RETURNS numeric(5,1) AS $$
BEGIN
    RETURN (date_part('day', v_interval) + date_part('hour', v_interval)/24)::numeric(5,1);
END;
$$ LANGUAGE plpgsql IMMUTABLE;


--------------------------------------------------------
-- Converts hex color representation to int
-- Parameters:
-- * hexval
--------------------------------------------------------
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
-- Contribution counts
-- Parameters:
-- * v_project_id
-- * v_date_interval_format ('YYYY-MM-01' for month)
-- * v_year - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_contributions(int, text, int);
CREATE FUNCTION fn_contributions(v_project_id int, v_date_interval_format text, v_year int)
    RETURNS table(date text, project_id int, contributor_count bigint, contribution_count bigint, core_team_contribution_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(cn.commited_at, v_date_interval_format) AS date,
        cn.project_id,
        count(DISTINCT cn.contributor_id) AS contributor_count,
        count(*) AS contribution_count,
        sum(c.is_core_member::int) AS core_team_contribution_count

    FROM contribution cn
        LEFT JOIN contributor c on cn.contributor_id = c.id
    WHERE
        is_maintenance_commit = FALSE
        AND (v_project_id IS NULL OR v_project_id = cn.project_id)
        AND (v_year IS NULL OR v_year = date_part('year', cn.commited_at))
    GROUP BY date, cn.project_id
    ORDER BY date asc;
END;
$$ LANGUAGE plpgsql;


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
  ORDER BY date ASC;

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
    ORDER BY date ASC;

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
-- Contributors who started contributing during the given year
-- with contribution counts
--
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


--------------------------------------------------------
-- Issues per date
--
-- Parameters:
-- * v_project_id
-- * v_date_interval_format ('YYYY-MM-01' for month)
-- * v_year - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_issues_per_date(int, text, int);
CREATE FUNCTION fn_issues_per_date(v_project_id int, v_date_interval_format text, v_year int)
    RETURNS table(date text, project_id int, issue_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(i.created_at, v_date_interval_format) AS date,
        i.project_id,
        count(i.id) as issue_count

    FROM issue i
    WHERE
        (v_project_id IS NULL OR v_project_id = i.project_id)
        AND (v_year IS NULL OR v_year = date_part('year', i.created_at))
    GROUP BY date, i.project_id
    ORDER BY date ASC;
END;
$$ LANGUAGE plpgsql;


--------------------------------------------------------
-- Issues made by contributors per date
--
-- Parameters:
-- * v_project_id
-- * v_date_interval_format ('YYYY-MM-01' for month)
-- * v_year - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_issues_of_contributors_per_date(int, text, int);
CREATE FUNCTION fn_issues_of_contributors_per_date(v_project_id int, v_date_interval_format text, v_year int)
    RETURNS table(date text, project_id int, issue_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(i.created_at, v_date_interval_format) AS date,
        i.project_id,
        count(i.id) as issue_count

    FROM issue i
        LEFT JOIN contributor c on c.github_id = i.github_user_id
    WHERE
        (v_project_id IS NULL OR v_project_id = i.project_id)
        AND (v_year IS NULL OR v_year = date_part('year', i.created_at))
        AND EXISTS (
             SELECT cn.id
             FROM contribution cn
             WHERE
                cn.contributor_id = c.id
                AND (v_project_id IS NULL OR v_project_id = cn.project_id)
		    )
    GROUP BY date, i.project_id
    ORDER BY date ASC;
END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------
-- Pull requests per date
--
-- Parameters:
-- * v_project_id
-- * v_date_interval_format ('YYYY-MM-01' for month)
-- * v_year - year of contribution
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_pull_requests_per_date(int, text, int);
CREATE FUNCTION fn_pull_requests_per_date(v_project_id int, v_date_interval_format text, v_year int)
    RETURNS table(date text, project_id int, pr_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(p.created_at, 'YYYY') AS date,
        p.project_id,
        count(p.id) as pr_count

    FROM pull_request p
    WHERE
        (v_project_id IS NULL OR v_project_id = p.project_id)
        AND (v_year IS NULL OR v_year = date_part('year', p.created_at))
    GROUP BY date, p.project_id
    ORDER BY date ASC;
END;
$$ LANGUAGE plpgsql;



--------------------------------------------------------
-- Issue labels
--
-- Parameters:
-- * v_project_id
-- * v_state
-- * v_year
-- * v_min_count
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_issue_labels(int, text, int, int);
CREATE FUNCTION fn_issue_labels(v_project_id int, v_state text, v_year int, v_min_count int)
    RETURNS table(label text, project_id int, issue_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        labels.label,
        labels.project_id,
        count(*) as issue_count
    FROM
    (
        SELECT
            i.id, i.project_id, i.state, i.created_at, unnest(string_to_array(i.labels, ',')) AS label
        FROM issue i
    ) labels
    WHERE
        (v_project_id IS NULL OR v_project_id = labels.project_id)
        AND (v_state IS NULL OR v_state = labels.state)
        AND (v_year IS NULL OR v_year = date_part('year', labels.created_at))
    GROUP BY labels.label, labels.project_id
    HAVING count(*) >= COALESCE(v_min_count, 1)
    ORDER BY issue_count DESC;

END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------
-- Top-contributors with pr and issue count
--
-- Parameters:
-- * v_project_id
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_contributor_pull_request_and_issue_count(int);
CREATE FUNCTION fn_contributor_pull_request_and_issue_count(v_project_id int)
    RETURNS table(name text, pr_count bigint, issue_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        c.name,
        count(DISTINCT pr.number) AS pr_count,
        count(DISTINCT i.number) AS issue_count

    FROM contributor c
    LEFT JOIN pull_request pr ON pr.github_user_id = c.github_id
    LEFT JOIN issue i ON i.github_user_id = c.github_id

    WHERE pr.project_id = v_project_id AND i.project_id = v_project_id
    GROUP BY c.name
    ORDER BY pr_count DESC;

END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------
-- Top-contributors with pr and pr review count
--
-- Parameters:
-- * v_project_id
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_contributor_pull_request_and_review_count(int);
CREATE FUNCTION fn_contributor_pull_request_and_review_count(v_project_id int)
  RETURNS table(name text, pr_count bigint, issue_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
      c.name,
      count(DISTINCT pr.number) AS pr_count,
      count(distinct prr.id) AS pr_review_count

    FROM contributor c
      LEFT JOIN pull_request pr ON pr.github_user_id = c.github_id
      LEFT JOIN pull_request_review prr ON prr.github_user_id = c.github_id
      LEFT JOIN pull_request prr_pr ON prr_pr.id = prr.pull_request_id

    WHERE pr.project_id = v_project_id AND prr_pr.project_id = v_project_id
    GROUP BY c.name
    ORDER BY pr_count DESC;

END;
$$ LANGUAGE plpgsql;


--------------------------------------------------------
-- Pull request labels
--
-- Parameters:
-- * v_project_id
-- * v_state
-- * v_year
-- * v_min_count
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_pull_request_labels(int, text, text, int, int, text);
CREATE FUNCTION fn_pull_request_labels(v_project_id int, v_state text, v_date_interval_format text, v_year int, v_min_count int, v_label text)
  RETURNS table(date text, label text, project_id int, pull_request_count bigint) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(labels.created_at, v_date_interval_format) AS date,
        labels.label,
        labels.project_id,
        count(*) as pull_request_count
    FROM
    (
        SELECT
            i.id, i.project_id, i.state, i.created_at, unnest(string_to_array(i.labels, ',')) AS label
        FROM pull_request i
    ) labels
    WHERE
        (v_project_id IS NULL OR v_project_id = labels.project_id)
        AND (v_state IS NULL OR v_state = labels.state)
        AND (v_label IS NULL OR v_label = labels.label)
        AND (v_year IS NULL OR v_year = date_part('year', labels.created_at))
    GROUP BY date, labels.label, labels.project_id
    HAVING count(*) >= COALESCE(v_min_count, 1)
    ORDER BY date, pull_request_count DESC;

END;
$$ LANGUAGE plpgsql;


/*

PRs made by own issues

SELECT c.name, count(distinct pr_number) as pr_count, count(distinct i.number)
FROM
	(
		select project_id, number as pr_number, github_user_id, unnest(string_to_array(issue_numbers, ','))::int as issue_number
		from pull_request
	) pr
left join issue i on i.number = pr.issue_number AND pr.project_id = i.project_id
left join contributor c on i.github_user_id = c.github_id
where i.github_user_id = pr.github_user_id
GROUP BY c.name
order by pr_count desc

 */

--------------------------------------------------------
-- Pull request count per state
--
-- Parameters:
-- * v_project_id
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_pull_request_per_state(int);
CREATE FUNCTION fn_pull_request_per_state(v_project_id int)
    RETURNS table(state text, pr_count bigint) AS $$
BEGIN
    RETURN query
    SELECT
           fn_pull_request_state(merged_at, closed_at) AS state, count(*)
    FROM pull_request
    WHERE project_id = v_project_id
    GROUP BY fn_pull_request_state(merged_at, closed_at);
END;
$$ LANGUAGE plpgsql;

--------------------------------------------------------
-- Pull request avg merge and close time per date
--
-- Parameters:
-- * v_project_id
-- * v_date_interval_format
--------------------------------------------------------
DROP FUNCTION IF EXISTS fn_pull_request_lifetime_per_date(int, text);
CREATE FUNCTION fn_pull_request_lifetime_per_date(v_project_id int, v_date_interval_format text)
    RETURNS table(date text, project_id int, avg_merge_time int, avg_close_time int) AS $$
BEGIN
    RETURN query

    SELECT
        to_char(p.created_at, v_date_interval_format) AS date,
        p.project_id,
        round(fn_interval_to_days(AVG(p.merged_at - p.created_at)))::int as avg_merge_time,
        round(fn_interval_to_days(AVG(p.closed_at - p.created_at)))::int as avg_close_time
    FROM pull_request p
    WHERE p.project_id = v_project_id
    GROUP BY date, p.project_id
    ORDER BY date ASC;
END;
$$ LANGUAGE plpgsql;
