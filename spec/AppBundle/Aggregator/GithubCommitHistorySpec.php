<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\Github\ClientAdapter;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubUser;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubCommitHistory
 */
class GithubCommitHistorySpec extends ObjectBehavior
{
    function let(ClientAdapter $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        ProjectRepository $projectRepository)
    {
        $this->beConstructedWith($githubApi, $contributorRepository, $projectRepository, $contributionRepository, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubCommitHistory::class);
    }

    function it_returns_aggregated_data(ClientAdapter $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        ProjectRepository $projectRepository,
        Project $project,
        Contributor $contributor)
    {
        $this->initDependencies($githubApi, $contributorRepository, $contributionRepository, $projectRepository, $project, $contributor);
        $report = $this->aggregate(['project_id' => 1]);
    }

    private function initDependencies(ClientAdapter $githubApi,
        ContributorRepository $contributorRepository,
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

        $user = GithubUser::createFromGithubResponseData([
            'name' => 'Frodo Baggins',
            'email' => 'frodo@shire',
            'location' => 'Bag End',
        ]);

        $githubApi
            ->getCommits(Argument::cetera())
            ->willReturn([$commit], null);

        $githubApi
            ->getUser(Argument::type('string'))
            ->willReturn($user);


        $contributorRepository
            ->findByGithubId(300)
            ->willReturn(null);

        $contributorRepository
            ->findByEmails(Argument::type('array'))
            ->willReturn(null);

        $contributorRepository
            ->saveContributor(Argument::type(Contributor::class))
            ->shouldBeCalled();

        $contributor
            ->getId()
            ->willReturn(300);

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
