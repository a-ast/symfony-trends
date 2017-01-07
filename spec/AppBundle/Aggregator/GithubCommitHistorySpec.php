<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\Github\GithubApi;
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
    function let(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository)
    {
        $this->beConstructedWith($githubApi, $contributorRepository, $contributionRepository, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubCommitHistory::class);
        $this->shouldImplement(AggregatorInterface::class);
    }

    function it_returns_aggregated_data(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        Project $project,
        Contributor $contributor)
    {
        $this->initDependencies($githubApi, $contributorRepository, $contributionRepository, $project, $contributor);
        $this->aggregate($project, []);
    }

    private function initDependencies(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
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
            ->getId()
            ->willReturn(1);

        $project
            ->getGithubPath()
            ->willReturn('github/path');

        $contributionRepository
            ->getLastCommitDate(Argument::type('integer'))
            ->willReturn(new \DateTimeImmutable('2016-01-01 00:00:00'));

        $contributionRepository
            ->store(Argument::any())
            ->shouldBeCalled();
    }
}
