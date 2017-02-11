<?php

use Behat\Behat\Context\Context;
use Github\Client;
use phpmock\MockBuilder;

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
     * @var integer|null
     */
    private $waitTime;

    /**
     * @var \phpmock\Mock
     */
    private $mock;

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
     * @When I send a request using a fake Github client
     */
    public function iSendARequest()
    {
        $this->enableSleepMock();

        try {
            $this->client->api('repo')->commits()->all('symfony', 'symfony', ['page' => 7]);
        } finally {
            $this->disableSleepMock();
        }
    }

    /**
     * @Then I wait :wait sec
     */
    public function iWaitGivenNumberOfSec($wait)
    {
        if ($this->waitTime != $wait) {
            throw new Exception(sprintf('Expected wait time %d does not match real time %d', $wait, $this->waitTime));
        }
    }

    /**
     * @return void
     */
    private function enableSleepMock()
    {
        $builder = new MockBuilder();

        $self = $this;

        $builder->setNamespace('Aa\ATrends\Api\Github')
            ->setName('sleep')
            ->setFunction(
                function ($seconds) use ($self) {
                     $self->waitTime = $seconds;
                }
            );


        $this->mock = $builder->build();
        $this->mock->enable();
    }

    /**
     * @return void
     */
    private function disableSleepMock()
    {
        $this->mock->disable();
    }
}

