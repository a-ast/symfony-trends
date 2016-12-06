<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\GeolocationApiClient;
use AppBundle\Client\GithubApiClient;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepository;
use Prophecy\Argument;
use Tests\AppBundle\TestCase;
use Tests\AppBundle\Traits\FixtureLoaderAwareTrait;
use Tests\AppBundle\Helper\RepositoryUtils;

class GithubCommitHistoryTest extends TestCase
{
    use FixtureLoaderAwareTrait;

    private $projectRepository;
    private $contributionRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->initFixtureLoader($this->getEntityManager(), __DIR__.'/fixtures');

        $this->projectRepository = $this->getService('repository.project');
        $this->contributionRepository = $this->getService('repository.contribution');
        $this->contributorRepository = $this->getService('repository.contributor');
    }

    public function testCreateNewContributorAndContributionInEmptyDb()
    {
        $this->fixtureLoader->loadFixtureFilesToDatabase(['orm/base.yml']);

        $commits = $this->fixtureLoader->getFixtureData('github-api/test1.yml');
        $users = $this->fixtureLoader->getFixtureData('github-api/users.yml');
        $locations = $this->fixtureLoader->getFixtureData('github-api/locations.yml');

        $aggregator = $this->getAggregator($commits, $users, $locations);
        $aggregator->aggregate(['project_id' => 1]);

        $this->assertEquals(2, RepositoryUtils::getRecordCount($this->contributorRepository));
        $this->assertEquals(3, RepositoryUtils::getRecordCount($this->contributionRepository));

        /** @var Contributor $contributor */
        $contributor = $this->contributorRepository->findOneBy(['email' => 'frodo@shire']);
        $this->assertEquals('Frodo Baggins', $contributor->getName());
        $this->assertEquals('frodo', $contributor->getGithubLogin());
        $this->assertEquals('Shire', $contributor->getCountry());
        $this->assertEquals('Bag End', $contributor->getGithubLocation());
        $this->assertEquals(['frodo.baggins@shire', 'frodo.b@shire'], $contributor->getGitEmails());
        $this->assertEquals(['frodo.b'], $contributor->getGitNames());

        /** @var Contributor $contributor */
        $contributor = $this->contributorRepository->findOneBy(['email' => 'sam@shire']);
        $this->assertEquals('Sam', $contributor->getName());
        $this->assertEquals('sam', $contributor->getGithubLogin());
        $this->assertEquals('Shire', $contributor->getCountry());
        $this->assertEquals('Shire', $contributor->getGithubLocation());
        $this->assertEquals(['samuel@shire'], $contributor->getGitEmails());
        $this->assertEquals([''], $contributor->getGitNames());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFailsIfProjectNotFound()
    {
        $aggregator = $this->getAggregator([], [], []);

        $aggregator->aggregate(['project_id' => 42]);
    }

    /**
     * @param array $commitHistory
     *
     * @return GithubCommitHistory
     */
    protected function getAggregator(array $commitHistory, array $users, array $locations)
    {
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
