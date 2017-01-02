Feature: Github Commit History Aggregator

  Scenario: Aggregate commits to empty database
    Given I have existing projects:
      | id | name        | label       | path               | color |
      | 1  | Three-rings | Three-rings | elrond/three-rings | #000  |
    When API commits:
      | sha     | date                 | message            | commit.author.id | commit.author.name | commit.author.email | author.login |
      | frodo-1 | 2016-11-22T00:13:33Z | Think about my way | 300              | frodo.b            | frodo.baggins@shire | frodo        |

