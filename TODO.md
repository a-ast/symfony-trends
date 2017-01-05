TODO
==============


Aggregators
1. UserCountry (using geocoder)
2. ContributorsPage (index contributor page, find sensiolabs user, find backlink to github)
3. SensiolabsConnect - index all that have github link (see above), get additional info

KW 1:

1. aggregator_github_commit_history.feature more and more tests
2. Get rid of phpunit tests (chaqnge to phpspec)
4. extract getting countries using geocoder !!!!!!!!!!!!!!!!!!!

KW 2:

1. Inject projects in aggregators
2. Aggregate commit changes

KW 3:

1. Aggregate PRs



Charts

- People to contributed monthly (most likely showing new contributors)
- Forks by version
- Symfony bundle usage in github


Check why this commit were missing

'e38be091cef535206cf1d6625806bf5f17095f53',
'3b5127dbe92960abc090a1fda960505e98faca0b',
'e66d3da91ac2890927f0aaa8bfbedfc9985599f0',
'999f769ba545e3eb9fb91df5e2fce7320834b9b2'


Improve solution for Githb API client

* use Mock for fake responses https://github.com/php-http/mock-client
* use bundle to simplify config http://docs.php-http.org/en/latest/integrations/symfony-bundle.html
