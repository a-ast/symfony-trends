Feature: Github Commits Aggregator

  Scenario: Aggregate 1 commit to the empty database
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | github_path   | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |
    And I request commits:
      | sha     | date                 | message | committer_id | committer_name | committer_email | committer_login |
      | frodo-1 | 2016-11-22T00:13:33Z | Ring?!  | 300          | frodo.b        | frodo@shire     | frodo           |
    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email       | name    | githubId | githubLogin | githubLocation | gitEmails | gitNames |
      | frodo@shire | frodo.b | 300      | frodo       |                |           |          |
    Then I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo-1    |


#  Scenario: Aggregate 2 commits of one contributor to the empty database
#    Git names and emails must be merged.
#    Given there are "AppBundle\Entity\Project" entities:
#      | name        | label       | github_path   | color |
#      | Three-rings | Three-rings | elrond/3rings | #000  |
#    And I request commits:
#      | sha     | date                 | message | committer_id | committer_name | committer_email | committer_login |
#      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | 300          | frodo.b        | frodo@shire     | frodo           |
#      | frodo-2 | 2016-11-33T00:00:00Z | No!!!   | 300          | frodo.b        | frodo@shire     | frodo           |
#    When I aggregate commits
#
#    Then I should see "AppBundle\Entity\Contributor" entities:
#      | email       | name    | githubId | githubLogin | githubLocation | gitEmails | gitNames |
#      | frodo@shire | frodo.b | 300      | frodo       |                |           |          |
#    Then I should see "AppBundle\Entity\Contribution" entities:
#      | projectId | contributorId | message | commitHash |
#      | 1         | 1             | Ring?!  | frodo-1    |
#      | 1         | 1             | No!!!   | frodo-2    |
