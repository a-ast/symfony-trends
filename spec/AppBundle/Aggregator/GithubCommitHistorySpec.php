<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\Github\ClientAdapter;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubCommitHistory
 */
class GithubCommitHistorySpec extends ObjectBehavior
{
    function let(ClientAdapter $githubApi,
        ContributorBuilder $contributorBuilder,
        ContributionRepository $contributionRepository,
        ProjectRepository $projectRepository)
    {
        $this->beConstructedWith($githubApi, $contributorBuilder, $projectRepository, $contributionRepository, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubCommitHistory::class);
    }


    function it_returns_aggregated_data(ClientAdapter $githubApi,
        ContributorBuilder $contributorBuilder,
        ContributionRepository $contributionRepository,
        ProjectRepository $projectRepository,
        Project $project,
        Contributor $contributor)
    {
        $this->initDependencies($githubApi, $contributorBuilder, $contributionRepository, $projectRepository, $project, $contributor);
        $report = $this->aggregate(['project_id' => 1]);
    }

    private function initDependencies(ClientAdapter $githubApi,
        ContributorBuilder $contributorBuilder,
        ContributionRepository $contributionRepository,
        ProjectRepository $projectRepository,
        Project $project,
        Contributor $contributor)
    {
        $commit = GithubCommit::createFromGithubResponseData([
            'sha' => 'hash-frodo-1',
            'commit' => [
                'author' => [
                    'name' => 'frodo.b',
                    'email' => 'frodo.baggins@shire',
                    'date' => '2016-11-22T00:13:33Z',
                ],
                'message' => 'Added thoughts about my future way',
            ],
            'author' => [
                'id' => 300,
                'login' => 'frodo',
            ],
        ]);

        $users = [
            [
                'name' => 'Frodo Baggins',
                'email' => 'frodo@shire',
                'location' => 'Bag End',
            ]
        ];

        $githubApi
            ->getCommitsByPage(Argument::cetera())
            ->willReturn([$commit], null);

        $githubApi
            ->getUser(Argument::type('string'))
            ->willReturn($users[0]);

        $contributorBuilder
            ->buildFromGithubCommit(Argument::type(GithubCommit::class))
            ->willReturn($contributor);

        $contributor
            ->getId()
            ->willReturn(100);

        $project
            ->getGithubPath()
            ->willReturn('github/path');

        $projectRepository
            ->find(Argument::any())
            ->willReturn($project);

        $contributionRepository
            ->getLastCommitDate(Argument::type('integer'))
            ->willReturn(new \DateTimeImmutable('2016-01-01 00:00:00'));

        $contributionRepository
            ->store(Argument::any())
            ->shouldBeCalled();

        $contributionRepository
            ->clear()
            ->shouldBeCalled();
    }
}
