<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Github\Client;

/**
 * Defines application features from the specific context.
 */
class ApiFeatureContext implements Context
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Initializes context.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @When I send a request
     */
    public function iSendARequest()
    {
        $commits = $this->client->api('repo')->commits()->all('symfony', 'symfony', ['page' => 7]);

    }

    /**
     * @Then the response has status code :arg1
     */
    public function theResponseHasStatusCode($arg1)
    {
        throw new PendingException();
    }

}
