Feature: Contributor Page Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate contributor logins to the empty database
    Given ContributorPage API returns contributors data:
      | login |
      | frodo |
      | bilbo |

    When I aggregate "contributor-pages" for project 1

    Then I should see "AppBundle\Entity\SensiolabsUser" entities:
      | id | contributorId | name  | login |
      | 1  | 0             | frodo | frodo |
      | 2  | 0             | bilbo | bilbo |


  Scenario: Aggregate contributor logins to the database with existing sensiolabs users

    Given there are "AppBundle\Entity\SensiolabsUser" entities:
      | contributorId | name             | login   | createdAt        | updatedAt        |
      | 1             | Gandalf the Grey | gandalf | date(2016-11-01) | date(2016-11-02) |

    And ContributorPage API returns contributors data:
      | login |
      | frodo |
      | bilbo |

    When I aggregate "contributor-pages" for project 1

    Then I should see "AppBundle\Entity\SensiolabsUser" entities:
      | id | contributorId | name             | login   |
      | 1  | 1             | Gandalf the Grey | gandalf |
      | 2  | 0             | frodo            | frodo   |
      | 3  | 0             | bilbo            | bilbo   |
