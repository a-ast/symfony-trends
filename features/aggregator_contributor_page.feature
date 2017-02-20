Feature: Contributor Page Aggregator

  Background:
    Given there are "Aa\ATrends\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregate contributor logins to the empty database
    Given ContributorPage API returns contributors data:
      | login |
      | frodo |
      | bilbo |

    Given Sensiolabs API returns profile data:
      | login | name          | country | githubUrl | facebookUrl | linkedInUrl | twitterUrl | websiteUrl  | blogUrl    | blogFeedUrl |
      | frodo | Frodo Baggins | Shire   | gh/frodo  | fb/frodo    | link/frodo  | tw/frodo   | frodo.shire | blog/frodo | feed/frodo  |
      | bilbo | Bilbo Baggins | Shire   | gh/bilbo  | fb/bilbo    | link/bilbo  | tw/bilbo   | bilbo.shire | blog/bilbo | feed/bilbo  |
    
    And there are "Aa\ATrends\Entity\Contributor" entities:
      | githubLogin | name  | email | gitEmails | gitNames |
      | frodo       | Frodo | fr@   | []        | []       |
      | bilbo       | Bilbo | blb@  | []        | []       |

    When I aggregate "contributor-pages" for project 1

    Then I should see "AppBundle\Entity\SensiolabsUser" entities:
      | id | contributorId | login | name          | country | facebookUrl | linkedInUrl | twitterUrl | websiteUrl  | blogUrl    | blogFeedUrl |
      | 1  | 1             | frodo | Frodo Baggins | Shire   | fb/frodo    | link/frodo  | tw/frodo   | frodo.shire | blog/frodo | feed/frodo  |
      | 2  | 2             | bilbo | Bilbo Baggins | Shire   | fb/bilbo    | link/bilbo  | tw/bilbo   | bilbo.shire | blog/bilbo | feed/bilbo  |
#
#
#  Scenario: Aggregate contributor logins to the database with existing sensiolabs users
#
#    Given there are "AppBundle\Entity\SensiolabsUser" entities:
#      | contributorId | name             | login   | createdAt        | updatedAt        |
#      | 1             | Gandalf the Grey | gandalf | date(2016-11-01) | date(2016-11-02) |
#
#    And ContributorPage API returns contributors data:
#      | login |
#      | frodo |
#      | bilbo |
#
#    When I aggregate "contributor-pages" for project 1
#
#    Then I should see "AppBundle\Entity\SensiolabsUser" entities:
#      | id | contributorId | name             | login   |
#      | 1  | 1             | Gandalf the Grey | gandalf |
#      | 2  | 0             | frodo            | frodo   |
#      | 3  | 0             | bilbo            | bilbo   |
