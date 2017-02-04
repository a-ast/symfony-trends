TODO
==============

### To think:

1. Aggregator ContributorCountru is project-agnostic, it must be specific interface or another flag to avoid run for every project.
1. Rename contribution table??
1. IMPORTANT: Recheck all issues (can be changed) or only last month, year?

### KW 4. Technical dept
1. Features for all aggregators
1. Implement contributor page agg (index contributor page, find sensiolabs user, find backlink to github)
1. Implement sensiolabs connect agg (index all that have github link (see above), get additional info)
   * use API https://github.com/sensiolabs/connect/blob/master/src/SensioLabs/Connect/Api/Entity/User.php
   * https://connect.sensiolabs.com/about/api
1. Get rid of aggregator config, use services directly


### KW 5.
1. Split bundle to lib + bundle
1. Review contributions aggregator see https://developer.github.com/v3/pulls/comments/

## KW 6.
1. Aggregate commit changes


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
