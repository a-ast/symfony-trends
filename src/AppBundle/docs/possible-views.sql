-- Contribution counts

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
select project_ids, count(*) as contributor_count
from (
       select contributor_id, string_agg(project_id::text, ','::text) as project_ids
       from (
              select distinct contributor_id, project_id
              from contribution
              order by project_id asc
            ) c
       group by contributor_id
     ) gc
group by project_ids
