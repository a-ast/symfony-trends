Feature: Github API Client

  Scenario: Github client authenticates requests
    When I send a request using a fake Github client
    Then I wait 60 sec

