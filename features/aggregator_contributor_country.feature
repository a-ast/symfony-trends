Feature: Contributor Country Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


  Scenario: Aggregator sets country for a contributor using their Github location

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | gitEmails | gitNames |
      | frodo | Bag end        |         | []        | []       |
    Given Geocoder API returns location data:
      | location  | country |
      | Bag end   | Shire   |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country |
      | frodo | Bag end        | Shire   |


  Scenario: Aggregator does not change country if it is already set

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | gitEmails | gitNames |
      | frodo | Bag end        | Shire   | []        | []       |
    Given Geocoder API returns location data:
      | location  | country |
      | Bag end   | Mordor  |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country |
      | frodo | Bag end        | Shire   |


  Scenario: Aggregator does not set country if it is not found in Geocoder API

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | gitEmails | gitNames |
      | frodo | Bag end        |         | []        | []       |
    Given Geocoder API returns location data:
      | location     | country |
      | Minas Tirith | Gondor  |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country |
      | frodo | Bag end        |         |

  Scenario: Aggregator does not set country if Geocoder API returns null as country name

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | gitEmails | gitNames |
      | frodo | Bag end        |         | []        | []       |
    Given Geocoder API returns location data:
      | location | country |
      | Bag end  | ~       |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country |
      | frodo | Bag end        |         |


  Scenario: Aggregator does not set country for contributors with an "is ignored location" flag

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | isIgnoredLocation | gitEmails | gitNames |
      | frodo | Bag end        |         | 1                 | []        | []       |
    Given Geocoder API returns location data:
      | location  | country |
      | Bag end   | Shire   |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | isIgnoredLocation |
      | frodo | Bag end        |         | 1                 |
