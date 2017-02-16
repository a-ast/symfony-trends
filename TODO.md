TODO
==============

### Next steps:

1. ContributorPageAggregator: iterate links, store to sensiolabs_users table.
1. Implement phpspec and behat scenarios.
1. Implement AggregatorReport
1. Implement PullRequestBodyProcessor and commit.is_maintenance using events.
1. Move ATrends to vendor
1. Integrate deptrack from sensiolabs to check dependencies

### To think:

1. Rename contribution table??
1. IMPORTANT: Recheck all issues (can be changed) or only last month, year?
1. Own implementation of SimpleArray? => fix nullable issue or/and implement normal array for postgresql 
1. Find a way to ignore all pull request import (maybe from given date or id)

### KW 4. Technical dept
1. Features for all aggregators
1. Implement sensiolabs connect agg (index all that have github link (see above), get additional info)
   * use API https://github.com/sensiolabs/connect/blob/master/src/SensioLabs/Connect/Api/Entity/User.php
   * https://connect.sensiolabs.com/about/api
1. Introduce a way increment/full/since aggregate
1. Think of AggregatorReport
1. Implement useful aggregator reports
1. Implement aggregator orchestrator/chain/...



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
