-- Get contributors for a project sorted by number of contributions
select c.name, cn.commit_count as cnt
from contributor c
left join contribution cn on cn.contributor_id = c.id
where cn.project_id = 1
order by cnt DESC


-- Get contributor count per version
select v.label, count(distinct c.contributor_id)
from project_version v
  left join contribution_history c on c.commited_at < v.released_at and c.project_id = v.project_id
where c.project_id = 1
group by v.label;
