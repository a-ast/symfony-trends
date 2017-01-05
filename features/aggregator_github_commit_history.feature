Feature: Github Commits Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate 1 commit without author information to the empty database
    Given Github returns commits:
      | sha     | date                 | message | commitAuthorName | commitAuthorEmail |
      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | frodo            | frodo@shire       |
    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email       | name  | githubId | githubLogin | githubLocation | gitEmails | gitNames |
      | frodo@shire | frodo |          |             |                | []        | []       |
    And I should see "AppBundle:Contribution" entities:
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
      | email       | name  | githubId | githubLogin | githubLocation | gitEmails | gitNames            |
      | frodo@shire | frodo |          |             |                | []        | [frodo.b,frodo.bag] |
    And I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo-1    |
      | 1         | 1             | No!!!   | frodo-2    |
      | 1         | 1             | Yes!    | frodo-3    |


  Scenario: Aggregate commits of one contributor with and without author information to the empty database
    Git names and emails must be merged.
    Location must be set.
    Given Github returns commits:
      | sha     | date                 | message | commitAuthorName | commitAuthorEmail | authorId | authorLogin |
      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | frodo            | frodo@shire       | ~        |             |
      | frodo-2 | 2016-11-23T00:00:00Z | No!!!   | frodo.b          | frodo@shire       | 100      | Frodo       |
    And Github returns users:
      | login | name       | email            | location |
      | Frodo | Frodo.user | frodo.user@shire | Bag End  |

    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email       | name  | githubId | githubLogin | githubLocation | gitEmails          | gitNames             |
      | frodo@shire | frodo | 100      | Frodo       | Bag End        | [frodo.user@shire] | [Frodo.user,frodo.b] |
    And I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo-1    |
      | 1         | 1             | No!!!   | frodo-2    |


  Scenario: Aggregate commits of the existing contributor found by Github id
    Given there are "AppBundle\Entity\Contributor" entities:
      | email        | name  | githubId | githubLogin | gitEmails        | gitNames |
      | frodo1@shire | frodo | 100      | Frodo1      | [frodo1.1@shire] | [frodo1] |

    And Github returns commits:
      | sha     | date                 | message | commitAuthorName | commitAuthorEmail | authorId |
      | frodo-1 | 2016-11-22T00:00:00Z | Ring?!  | frodo2           | frodo2@shire      | 100      |

    When I aggregate commits

    Then I should see "AppBundle\Entity\Contributor" entities:
      | email        | name  | githubId | githubLogin | gitEmails                     | gitNames        |
      | frodo1@shire | frodo | 100      | Frodo1      | [frodo1.1@shire,frodo2@shire] | [frodo1,frodo2] |


  Scenario: Aggregate commits of different contributors
    Given Github returns commits:
      | sha   | date                 | message | commitAuthorEmail |
      | frodo | 2016-11-22T00:00:00Z | Ring?!  | frodo@shire       |
      | sam   | 2016-11-24T00:00:00Z | Frodo?  | sam@shire         |

    When I aggregate commits

    And I should see "AppBundle:Contribution" entities:
      | projectId | contributorId | message | commitHash |
      | 1         | 1             | Ring?!  | frodo      |
      | 1         | 2             | Frodo?  | sam        |
