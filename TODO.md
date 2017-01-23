TODO
==============

### To think:

1. Rename contribution table??
1. Fix Aggregator naming
1. PullRequestBodyParser - extract issue number and other properties.


#### Aggregators
1. UserCountry (using geocoder)
2. ContributorsPage (index contributor page, find sensiolabs user, find backlink to github)
3. SensiolabsConnect - index all that have github link (see above), get additional info


### KW 2:

1. Get rid of multiple trend aggregators per project
2. Fork aggregator: check if exists to avoid removing all records by an update

### KW 3:

1. Aggregate PRs
2. Aggregate commit changes


# Charts

- Real Contributors: x - commit count, y - age of last commit (frequency?)

- Contributors vs others: x - issue count, y - smth else(?)

- PR/Issue Issue closers (rating, avarage time) 
- People to contributed monthly (most likely showing new contributors)
- Symfony bundle usage in github


Check why this commit were missing

'e38be091cef535206cf1d6625806bf5f17095f53',
'3b5127dbe92960abc090a1fda960505e98faca0b',
'e66d3da91ac2890927f0aaa8bfbedfc9985599f0',
'999f769ba545e3eb9fb91df5e2fce7320834b9b2'


Improve solution for Github API client

* use Mock for fake responses https://github.com/php-http/mock-client
* use bundle to simplify config http://docs.php-http.org/en/latest/integrations/symfony-bundle.html
