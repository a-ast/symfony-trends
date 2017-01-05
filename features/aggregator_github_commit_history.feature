Feature: Github Commits Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | github_path   | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |

  Scenario: Aggregate 1 commit without author information to the empty database
    Given Github returns commits:
      | sha     | date                 | message | commitAuthorName | commitAuthorEmail |
      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | frodo            | frodo@shire       |
    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email       | name  | githubId | githubLogin | githubLocation | gitEmails | gitNames |
      | frodo@shire | frodo |          |             |                |           |          |
    Then I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo-1    |


  Scenario: Aggregate commits of one contributor without author information to the empty database
    Git names must be merged.
    Given Github returns commits:
      | sha     | date                 | message | commitAuthorName | commitAuthorEmail |
      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | frodo            | frodo@shire       |
      | frodo-2 | 2016-11-23T00:00:00Z | No!!!   | frodo.b          | frodo@shire       |
      | frodo-3 | 2016-11-24T00:00:00Z | Yes!    | frodo.bag        | frodo@shire       |
    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email       | name  | githubId | githubLogin | githubLocation | gitEmails | gitNames          |
      | frodo@shire | frodo |          |             |                |           | frodo.b,frodo.bag |
    Then I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo-1    |
      | 1         | 1             | No!!!   | frodo-2    |
      | 1         | 1             | Yes!    | frodo-3    |
