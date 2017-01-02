Feature: Github Commit History Aggregator

  Scenario: Aggregate commits to empty database
    Given I have existing projects:
      | id | name        | label       | path               | color |
      | 1  | Three-rings | Three-rings | elrond/three-rings | #000  |
    And I request commits:
      | sha     | date                 | message | committer_id | committer_name | committer_email | committer_login |
      | frodo-1 | 2016-11-22T00:13:33Z | Ring?!  | 300          | frodo.b        | frodo@shire     | frodo           |
    When I aggregate commits

    Then I should see these contributors in the database:
      | email       | name    | githubId | githubLogin | country | githubLocation | gitEmails | gitNames |
      | frodo@shire | frodo.b | 300      | frodo       |         |                |           |          |

