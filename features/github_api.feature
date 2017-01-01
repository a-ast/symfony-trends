Feature: Github API Client

  Scenario: Github client authenticates requests
    When I send a request
    Then the response has status code 200

