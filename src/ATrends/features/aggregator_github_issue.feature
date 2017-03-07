Feature: Github Issues Aggregator

  Background:
    Given there are "Aa\ATrends\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |



  Scenario: Aggregate issues to the empty database
    It should create one issue and one pull request with githubId = 0.
    Given Github API returns issues data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | labels  | pullRequest |
      | 10 | 111    | opened | Issue 1 | 100    | Issue body 1 | 2016-11-01 | 2016-11-02 | ~          | [L1,L2] | ~           |
      | 20 | 222    | closed | Issue 2 | 200    | Issue body 2 | 2016-11-02 | 2016-11-03 | 2016-11-04 | []      | []          |

    When I aggregate issues for project 1

    Then I should see "Aa\ATrends\Entity\Issue" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels  |
      | 1  | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~                | [L1,L2] |

    Then I should see "Aa\ATrends\Entity\PullRequest" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels  |
      | 1  | 1         | 0        | 222    | closed | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | []      |


  Scenario: Aggregate issues to the existing database, existing issues und pull requests have been updated
    Given there are "Aa\ATrends\Entity\Issue" entities:
      | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt | labels  |
      | 1         | 10       | 111    | opened | Issue 1 | 100          | Issue body 1 | date(2016-11-01) | date(2016-11-02) | ~        | [L1,L2] |

    And there are "Aa\ATrends\Entity\PullRequest" entities:
      | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt | labels  | mergeSha | headSha | baseSha | baseRef |
      | 1         | 20       | 222    | opened | Issue 2 | 200          | Issue body 2 | date(2016-11-02) | date(2016-11-03) | ~        | [L1,L2] |          |         |         |         |

    And Github API returns issues data:
      | id | number | state  | title   | userId | body         | createdAt  | updatedAt  | closedAt   | labels     | pullRequest |
      | 10 | 111    | closed | Issue X | 100    | Issue body X | 2016-11-01 | 2016-11-02 | 2016-11-04 | [L1,L2,L3] | ~           |
      | 20 | 222    | opened | Issue Y | 200    | Issue body Y | 2016-11-02 | 2016-11-03 | 2016-11-04 | []         | []          |
      | 30 | 333    | opened | Issue 3 | 300    | Issue body 3 | 2016-11-03 | 2016-11-04 | 2016-11-05 | [L1]       | ~           |

    When I aggregate issues for project 1

    Then I should see "Aa\ATrends\Entity\Issue" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels     |
      | 1  | 1         | 10       | 111    | closed | Issue X | 100          | Issue body X | date(2016-11-01) | date(2016-11-02) | date(2016-11-04) | [L1,L2,L3] |
      | 2  | 1         | 30       | 333    | opened | Issue 3 | 300          | Issue body 3 | date(2016-11-03) | date(2016-11-04) | date(2016-11-05) | [L1]       |

    And I should see "Aa\ATrends\Entity\PullRequest" entities:
      | id | projectId | githubId | number | state  | title   | githubUserId | body         | createdAt        | updatedAt        | closedAt         | labels     |
      | 1  | 1         | 20       | 222    | opened | Issue Y | 200          | Issue body Y | date(2016-11-02) | date(2016-11-03) | date(2016-11-04) | []         |
