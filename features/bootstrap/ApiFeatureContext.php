<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Github\Client;
use phpmock\functions\SleepFunction;
use phpmock\MockBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

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
     * @param EventDispatcherInterface $dispatcher
     * @param Client $client
     */
    public function __construct(EventDispatcherInterface $dispatcher, Client $client)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;

        $this->dispatcher->addListener('github_api.before_wait_for_rate_limit_recovery', [$this, 'onGithubApiWait']);
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


    public function onGithubApiWait(GenericEvent $event)
    {
        $this->waitTime = $event->getArgument('wait');
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
        $builder->setNamespace('AppBundle\Client\Github')
            ->setName('sleep')
            ->setFunctionProvider(new SleepFunction([]));

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

