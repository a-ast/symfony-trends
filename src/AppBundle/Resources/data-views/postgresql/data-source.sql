DROP FUNCTION IF EXISTS hex_to_int(varchar) CASCADE;
CREATE OR REPLACE FUNCTION hex_to_int(hexval varchar) RETURNS integer AS $$
DECLARE
  result  int;
BEGIN
  EXECUTE 'SELECT x''' || hexval || '''::int' INTO result;
  RETURN result;
END;
$$ LANGUAGE plpgsql IMMUTABLE STRICT;


-- Contribution counts
DROP VIEW IF EXISTS vw_contributions_per_year CASCADE;
CREATE VIEW vw_contributions_per_year AS
  select
      to_char(cn.commited_at, 'YYYY') as date,
      cn.project_id,
      count(*) as contribution_count,
      sum(cast(c.is_core_member as int)) as core_team_contribution_count
  from contribution cn
  left join contributor c on cn.contributor_id = c.id
  -- where project_id = 1
  group by to_char(cn.commited_at, 'YYYY'), cn.project_id
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
           STRING_AGG(project_id::text, ','::text) as project_ids,
           STRING_AGG(project_name, ' and '::text) as project_names,
           TO_HEX(AVG(HEX_TO_INT(color))::int) AS color
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
