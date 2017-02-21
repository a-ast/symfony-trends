<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\CommitAggregator;
use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Api\Github\GithubApi;
use Aa\ATrends\Entity\Contributor;
use Aa\ATrends\Entity\Project;
use Aa\ATrends\Model\GithubCommit as ModelGithubCommit;
use Aa\ATrends\Model\GithubUser;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Progress\ProgressNotifierInterface;
use Aa\ATrends\Progress\EventBasedProgressNotifier;
use Aa\ATrends\Repository\ContributionRepository;
use Aa\ATrends\Repository\ContributorRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin CommitAggregator
 */
class CommitAggregatorSpec extends ObjectBehavior
{
    function let(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        EventBasedProgressNotifier $progressNotifier)
    {
        $this->beConstructedWith($githubApi, $contributorRepository, $contributionRepository, []);
        $this->setProgressNotifier($progressNotifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CommitAggregator::class);
        $this->shouldImplement(ProjectAwareAggregatorInterface::class);
    }

    function it_aggregates(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        ProjectInterface $project,
        Contributor $contributor,
        AggregatorOptionsInterface $options)
    {
        $this->initDependencies($githubApi, $contributorRepository, $contributionRepository, $project, $contributor);
        $this->setProject($project);
        $this->aggregate($options);
    }

    private function initDependencies(GithubApi $githubApi,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        ProjectInterface $project,
        Contributor $contributor)
    {
        $commit = ModelGithubCommit::createFromResponseData([
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

        $user = GithubUser::createFromResponseData([
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
