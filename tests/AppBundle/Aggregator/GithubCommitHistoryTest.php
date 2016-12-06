<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\GeolocationApiClient;
use AppBundle\Aggregator\Helper\GithubApiClient;
use Tests\AppBundle\FixtureLoader;
use Prophecy\Argument;
use Tests\AppBundle\TestCase;

class GithubCommitHistoryTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testFailsIfProjectNotFound()
    {
        $aggregator = $this->getAggregator();

        $aggregator->aggregate(['project_id' => 42]);

    }

    /**
     * @return GithubCommitHistory
     */
    protected function getAggregator()
    {
        //Fixtures::load(__DIR__.'/fixtures/commit_history.yml', $this->getObjectManager());

        $this->fixtureLoader->setFixtureDir(__DIR__.'/fixtures');
        $commits = $this->fixtureLoader->getFixtureData('GithubApi/basic.yml');

        $githubApi = $this->prophesize(GithubApiClient::class);
        $githubApi
            ->getCommits(Argument::cetera())
            ->willReturn($commits);

        $geoApi = $this->prophesize(GeolocationApiClient::class);

        $aggregator = new GithubCommitHistory($githubApi->reveal(), $geoApi->reveal(),
            $this->getService('repository.project'), $this->getService('repository.contribution'),
            $this->getService('repository.contributor'));

        return $aggregator;
    }
}
