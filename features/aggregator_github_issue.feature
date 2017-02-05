Feature: Github Issues Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate issues to the empty database
    Given Github API returns issues data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | labels  |
      | 10 | 111    | opened | Issue 1 | 100    | Issue body 1 | 2016-11-01 | 2016-11-02 | ~          | [L1,L2] |
      | 20 | 222    | closed | Issue 2 | 200    | Issue body 2 | 2016-11-02 | 2016-11-03 | 2016-11-04 | []      |

    When I aggregate issues for project 1

    Then I should see "AppBundle\Entity\Issue" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels  |
      | 1  | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~                | [L1,L2] |
      | 2  | 1         | 20       | 222    | closed | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | []      |


  Scenario: Aggregate issues to the existing database, existing issues updated
    Given there are "AppBundle\Entity\Issue" entities:
      | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels  |
      | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~                | [L1,L2] |

    And Github API returns issues data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | labels     |
      | 10 | 111    | closed | Issue X | 100    | Issue body X | 2016-11-01 | 2016-11-02 | 2016-11-04 | [L1,L2,L3] |
      | 20 | 222    | opened | Issue 2 | 200    | Issue body 2 | 2016-11-02 | 2016-11-03 | 2016-11-04 | []         |

    When I aggregate issues for project 1

    Then I should see "AppBundle\Entity\Issue" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels     |
      | 1  | 1         | 10       | 111    | closed | Issue X | 100          | Issue body X | date(2016-11-01) | date(2016-11-02) | date(2016-11-04) | [L1,L2,L3] |
      | 2  | 1         | 20       | 222    | opened | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | []         |
