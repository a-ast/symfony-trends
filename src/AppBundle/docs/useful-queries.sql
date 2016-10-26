-- Get contributors for a project sorted by number of contributions
select c.name, cn.commit_count as cnt
from contributor c
left join contribution cn on cn.contributor_id = c.id
where cn.project_id = 1
order by cnt DESC
