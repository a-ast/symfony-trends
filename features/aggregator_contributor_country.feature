Feature: Contributor Country Aggregator

  Background:
    Given there are "AppBundle\Entity\Project" entities:
      | name        | label       | githubPath    | color |
      | Three-rings | Three-rings | elrond/3rings | #000  |


#  Scenario: Aggregate contributor country using Github location
#
#    Given there are "AppBundle\Entity\Contributor" entities:
#      | name  | githubLocation | country | gitEmails | gitNames |
#      | frodo | Bag end        |         | []        | []       |
#    Given Geocoding API returns location data:
#      | location  | country |
#      | Bag end   | Shire   |
#    When I aggregate "contributor countries" for project 1
#
#    Then I should see "AppBundle\Entity\Contributor" entities:
#      | name  | githubLocation | country |
#      | frodo | Bag end        | Shire   |
#
#
#  Scenario: Aggregator does not change country if it is already set
#
#    Given there are "AppBundle\Entity\Contributor" entities:
#      | name  | githubLocation | country | gitEmails | gitNames |
#      | frodo | Bag end        | Shire   | []        | []       |
#    Given Geocoding API returns location data:
#      | location  | country |
#      | Bag end   | Mordor  |
#    When I aggregate "contributor countries" for project 1
#
#    Then I should see "AppBundle\Entity\Contributor" entities:
#      | name  | githubLocation | country |
#      | frodo | Bag end        | Shire   |


  Scenario: Aggregator does not set country if it is not found in Geocoder API

    Given there are "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country | gitEmails | gitNames |
      | frodo | Bag end        |         | []        | []       |
    Given Geocoding API returns location data:
      | location     | country |
      | Minas Tirith | Gondor  |
    When I aggregate "contributor countries" for project 1

    Then I should see "AppBundle\Entity\Contributor" entities:
      | name  | githubLocation | country |
      | frodo | Bag end        |         |
