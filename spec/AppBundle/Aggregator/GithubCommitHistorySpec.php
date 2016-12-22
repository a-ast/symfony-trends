<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Client\GeolocationApiClient;
use AppBundle\Client\GithubApiClient;
use AppBundle\Entity\Project;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tests\AppBundle\FixtureReader;

/**
 * @mixin GithubCommitHistory
 */
class GithubCommitHistorySpec extends ObjectBehavior
{
    function let(GithubApiClient $githubApi,
        GeolocationApiClient $geoApi,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository,
        ContributorRepository $contributorRepository)
    {
        $this->beConstructedWith($githubApi, $geoApi, $projectRepository, $contributionRepository, $contributorRepository, []);

        $commits = [
                    [
                        'sha' => 'hash-frodo-1',
                        'commit' =>
                            [
                                'author' =>
                                    [
                                        'id' => 300,
                                        'name' => 'frodo.b',
                                        'email' => 'frodo.baggins@shire',
                                        'date' => '2016-11-22T00:13:33Z',
                                    ],
                                'message' => 'Added thoughts about my future way',
                            ],
                        'author' =>
                            [
                                'id' => 300,
                                'login' => 'frodo',
                                'type' => 'User',
                            ],
                    ],
                ];

        $users = [
            [
                'name' => 'Frodo Baggins',
                'email' => 'frodo@shire',
                'location' => 'Bag End',
            ]
        ];

        $githubApi
            ->getCommits(Argument::cetera())
            ->willReturn($commits);

        $githubApi
            ->getUser(Argument::type('string'))
            ->willReturn($users[0]);

        $geoApi
            ->findCountry(Argument::type('string'))
            ->willReturn(['country' => 'Shire']);

        $projectRepository
            ->find(Argument::any())
            ->willReturn(new Project());

        $contributionRepository
            ->getLastCommitDate(Argument::any())
            ->willReturn(new \DateTimeImmutable('2016-01-01 00:00:00'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubCommitHistory::class);
    }

    function it_returns_aggregated_data()
    {
        $report = $this->aggregate(['project_id' => 1]);
    }
}
