<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\IssueAggregator;
use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\Issue;
use Aa\ATrends\Entity\Project;
use Aa\ATrends\Api\Github\Model\GithubIssue as ModelGithubIssue;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Progress\ProgressNotifierInterface;
use Aa\ATrends\Progress\EventBasedProgressNotifier;
use Aa\ATrends\Repository\IssueRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin IssueAggregator
 */
class IssueAggregatorSpec extends ObjectBehavior
{
    function let(GithubApiInterface $githubApi, IssueRepository $issueRepository,
        EventBasedProgressNotifier $progressNotifier)
    {
        $this->beConstructedWith($githubApi, $issueRepository);
        $this->setProgressNotifier($progressNotifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IssueAggregator::class);
        $this->shouldImplement(ProjectAwareAggregatorInterface::class);
    }

    function it_returns_aggregated_data(
        ProjectInterface $project,
        GithubApiInterface $githubApi,
        IssueRepository $issueRepository,
        OptionsInterface $options
    ) {
        $this->initDependencies($project, $githubApi, $issueRepository);

        $this->setProject($project);
        $this->aggregate($options);
    }

    /**
     * @param Project $project
     * @param GithubApiInterface $githubApi
     * @param IssueRepository $issueRepository
     */
    protected function initDependencies(
        ProjectInterface $project,
        GithubApiInterface $githubApi,
        IssueRepository $issueRepository
    ) {
        $project
            ->getId()
            ->willReturn(1);

        $project
            ->getGithubPath()
            ->willReturn('valinor/path');

        $issue = ModelGithubIssue::createFromResponseData([
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I need the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...but I fear.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'labels' => [
                ['id' => 1, 'name' => 'Bug'],
                ['id' => 2, 'name' => 'Feature'],
            ],
        ]);

        $githubApi
            ->getIssues('valinor/path', Argument::type(\DateTimeInterface::class))
            ->willReturn([$issue]);

        $issueRepository
            ->getLastCreatedAt(Argument::type('int'))
            ->willReturn(new \DateTimeImmutable('2010-10-11'))
            ->shouldBeCalled();

        $issueRepository
            ->findOneBy(Argument::type('array'))
            ->willReturn(new Issue());

        $issueRepository
            ->persist(Argument::type(Issue::class))
            ->shouldBeCalled();

        $issueRepository
            ->flush()
            ->shouldBeCalled();
    }
}
