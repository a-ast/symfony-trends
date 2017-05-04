-- RESULTS. Contributors
select c.id, c.name, count(*) cnt
from
  (

    select
      pc.pull_request_id, date_trunc('day', pc.created_at) as date, pc.github_user_id
    from pull_request_comment pc
      left join contributor c on c.github_id = pc.github_user_id
      left join pull_request pr on pr.number = pc.pull_request_id
                                   and pr.github_user_id != pc.github_user_id
    group by pc.pull_request_id, date, pc.github_user_id
  ) as review
  left join contributor c on c.github_id = review.github_user_id
group by c.id, c.name
order by cnt desc;


-- Results.stof by years
select date_part('year', review.date) as year, count(*) cnt
from
  (

    select
      pc.pull_request_id, date_trunc('day', pc.created_at) as date, pc.github_user_id
    from pull_request_comment pc
      left join contributor c on c.github_id = pc.github_user_id
      left join pull_request pr on pr.number = pc.pull_request_id
                                   and pr.github_user_id != pc.github_user_id
    group by pc.pull_request_id, date, pc.github_user_id
  ) as review
  left join contributor c on c.github_id = review.github_user_id
where  c.id = 41
group by  date_part('year', review.date)
order by year asc;


-- Raw stats by contributor
-- shows comment for own Ps (must me excluded)
-- no grouping by day

select c.id, c.name, count(*) cnt
from pull_request_comment pc
left join contributor c on c.github_id = pc.github_user_id
-- left join pull_request pr on pr.number = pc.pull_request_id
group by c.id, c.name
order by cnt desc;

-- Raw grouping by year
select date_part('year', pc.created_at) as year, count(*) cnt
from pull_request_comment pc
  left join contributor c on c.github_id = pc.github_user_id
where  c.id = 41
group by  date_part('year', pc.created_at)
order by year asc;

-- All comments by giving pr number
select
  pr.github_user_id, c.github_login, c.name, *
from pull_request_comment pc
  left join contributor c on c.github_id = pc.github_user_id
  left join pull_request pr on pr.number = pc.pull_request_id
where pc.pull_request_id  = 22619
      and pr.github_user_id != pc.github_user_id
order by pull_request_review_id asc;


-- Real code comments
select
  date_trunc('day', pc.created_at) as date, pc.github_user_id
from pull_request_comment pc
  left join contributor c on c.github_id = pc.github_user_id
  left join pull_request pr on pr.number = pc.pull_request_id
where pc.pull_request_id  = 17192
      and pr.github_user_id != pc.github_user_id
group by date, pc.github_user_id
order by date asc;
