Feature: Github Commits Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate forks to the empty database
    Given Github API returns forks data:
      | id | ownerId | createdAt            | updatedAt            | pushedAt             |
      | 10 | 100     | 2016-11-01T00:00:00Z | 2016-11-02T00:00:00Z | 2016-11-03T00:00:00Z |
      | 20 | 200     | 2016-12-01T00:00:00Z | 2016-12-02T00:00:00Z | 2016-12-03T00:00:00Z |
    When I aggregate forks for project 1

    Then I should see "AppBundle\Entity\Fork" entities:
      | id | projectId | githubId | ownerGithubId | createdAt        | updatedAt        | pushedAt         |
      | 1  | 1         | 10       | 100           | date(2016-11-01) | date(2016-11-02) | date(2016-11-03) |
      | 2  | 1         | 20       | 200           | date(2016-12-01) | date(2016-12-02) | date(2016-12-03) |


  Scenario: Aggregate forks to the existing database, existing forks ignored
    Given there are "AppBundle\Entity\Fork" entities:
      | projectId | githubId | ownerGithubId | createdAt        | updatedAt        | pushedAt         |
      | 1         | 20       | 200           | date(2016-11-01) | date(2016-11-02) | date(2016-11-03) |
    And Github API returns forks data:
      | id | ownerId | createdAt            | updatedAt            | pushedAt             |
      | 10 | 100     | 2016-11-01T00:00:00Z | 2016-11-02T00:00:00Z | 2016-11-03T00:00:00Z |
      | 20 | 200     | 2016-12-01T00:00:00Z | 2016-12-02T00:00:00Z | 2016-12-03T00:00:00Z |
    When I aggregate forks for project 1

    Then I should see "AppBundle\Entity\Fork" entities:
      | id | projectId | githubId | ownerGithubId | createdAt        | updatedAt        | pushedAt         |
      | 1  | 1         | 20       | 200           | date(2016-11-01) | date(2016-11-02) | date(2016-11-03) |
      | 2  | 1         | 10       | 100           | date(2016-11-01) | date(2016-11-02) | date(2016-11-03) |
