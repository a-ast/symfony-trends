Feature: Github Pull Request Aggregator

  Background:
    Given there are "Aa\ATrends\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate pull requests to the empty database
    Given Github API returns "pull-requests" data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | mergedAt   | baseRef |
      | 10 | 111    | opened | Issue 1 | 100    | Issue body 1 | 2016-11-01 | 2016-11-02 | ~          | ~          | 1.0     |
      | 20 | 222    | closed | Issue 2 | 200    | Issue body 2 | 2016-11-02 | 2016-11-03 | 2016-11-04 | 2016-11-04 | master  |

    When I aggregate "pull-requests" for project 1

    Then I should see "Aa\ATrends\Entity\PullRequest" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | mergedAt         | baseRef |
      | 1  | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~                | ~                | 1.0     |
      | 2  | 1         | 20       | 222    | closed | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | date(2016-11-04) | master  |


  Scenario: Aggregate pull requests to the existing database, existing pull requests updated
    Given there are "Aa\ATrends\Entity\PullRequest" entities:
      | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt | mergedAt | baseRef | mergeSha | headSha | baseSha |
      | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~        | ~        | 1.0     |          |         |         |

    And Github API returns "pull-requests" data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | mergedAt   | baseRef |
      | 10 | 111    | closed | Issue X | 100    | Issue body X | 2016-11-01 | 2016-11-02 | 2016-11-04 | 2016-11-05 | 1.1     |
      | 20 | 222    | opened | Issue 2 | 200    | Issue body 2 | 2016-11-02 | 2016-11-03 | 2016-11-04 | ~          |         |

    When I aggregate "pull-requests" for project 1

    Then I should see "Aa\ATrends\Entity\PullRequest" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | mergedAt         | baseRef |
      | 1  | 1         | 10       | 111    | closed | Issue X | 100          | Issue body X | date(2016-11-01) | date(2016-11-02) | date(2016-11-04) | date(2016-11-05) | 1.1     |
      | 2  | 1         | 20       | 222    | opened | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | ~                |         |
