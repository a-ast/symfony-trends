-- Get contributors for a project sorted by number of contributions
select contributor_id, count(*) as cnt
from contribution_log
where project_id = 1
group by contributor_id
order by cnt desc;

select c.name, count(cn.id) as cnt
from contribution2 cn
  left join contributor2 c on c.id = cn.contributor_id
group by c.name
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


select first_commit_at, count(*) as contributor_count
from (
       select contributor_id, min(commited_at) as first_commit_at
       from contribution_log
       where project_id = 1
       group by contributor_id
     ) fc

group by first_commit_at
order by first_commit_at asc;

-- Contributor count per country
select sensiolabs_country, count(*) as cnt
from contributor
group by sensiolabs_country
order by cnt desc;
