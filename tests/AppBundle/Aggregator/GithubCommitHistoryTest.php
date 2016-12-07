<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\GeolocationApiClient;
use AppBundle\Client\GithubApiClient;
use AppBundle\Repository\ContributorRepository;
use Prophecy\Argument;
use Symfony\Component\Yaml\Yaml;
use Tests\AppBundle\TestCase;
use Tests\AppBundle\Helper\RepositoryUtils;

class GithubCommitHistoryTest extends TestCase
{
    private $projectRepository;
    private $contributionRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var array
     */
    private $users;

    /**
     * @var array
     */
    private $locations;

    protected function setUp()
    {
        parent::setUp();

        $this->projectRepository = $this->getService('repository.project');
        $this->contributionRepository = $this->getService('repository.contribution');
        $this->contributorRepository = $this->getService('repository.contributor');

        $this->users = $this->getFixtureReader()->getFixtureData('github-api/users.yml');
        $this->locations = $this->getFixtureReader()->getFixtureData('github-api/locations.yml');
    }

    /**
     * @dataProvider provideTestCases
     *
     * @param array $databaseFixtures
     * @param array $commits
     * @param array $expected
     */
    public function testAggregate(array $databaseFixtures, $commits, $expected)
    {
        $this->getFixtureLoader()->load($databaseFixtures);

        $aggregator = $this->getAggregator($commits);
        $aggregator->aggregate(['project_id' => 1]);

        $contributors = RepositoryUtils::fetchAll($this->contributorRepository, 'email');
        $contributions = RepositoryUtils::fetchAll($this->contributionRepository, 'commit_hash');

        $this->assertEqualsToFixtureData($expected['contributors'], $contributors, 'email');
        $this->assertEqualsToFixtureData($expected['contributions'], $contributions, 'commit_hash');

    }

    public function provideTestCases()
    {
        $contents = file_get_contents(__DIR__.'/fixtures/github-api/test-cases.yml');

        return Yaml::parse($contents);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFailsIfProjectNotFound()
    {
        $aggregator = $this->getAggregator([]);

        $aggregator->aggregate(['project_id' => 42]);
    }

    /**
     * @param array $commitHistory
     *
     * @return GithubCommitHistory
     */
    protected function getAggregator(array $commitHistory)
    {
        $users = $this->users;
        $locations = $this->locations;

        $githubApi = $this->prophesize(GithubApiClient::class);
        $githubApi
            ->getCommits(Argument::cetera())
            ->willReturn($commitHistory, null);
        $githubApi
            ->getUser(Argument::type('string'))
            ->will(function ($args) use ($users) {
                return $users[$args[0]];
            });

        $geoApi = $this->prophesize(GeolocationApiClient::class);
        $geoApi
            ->findCountry(Argument::type('string'))
            ->will(function ($args) use ($locations) {
                return $locations[$args[0]];
            });

        $aggregator = new GithubCommitHistory($githubApi->reveal(), $geoApi->reveal(),
            $this->projectRepository, $this->contributionRepository, $this->contributorRepository);

        return $aggregator;
    }
}
