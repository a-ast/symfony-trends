-- Get contributors for a project sorted by number of contributions
select contributor_id, count(*) as cnt
from contribution_log
where project_id = 1
group by contributor_id
order by cnt desc;


-- Get contributor count in date intervals
select v.label, count(distinct c.contributor_id)
from project_version v
  left join contribution_log c on c.commited_at < v.released_at and c.project_id = v.project_id
where c.project_id = 1
group by v.label;

-- Get unique contributors per date????
-- select distinct(contributor_id), commited_at
-- from contribution_log
-- where project_id = 1
-- order by commited_at desc;

-- Get first contributions
select contributor_id, min(commited_at)
from contribution_log
where project_id = 1
group by contributor_id
order by min(commited_at) asc;
