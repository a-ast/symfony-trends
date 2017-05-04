<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\PullRequestComment;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Repository\PullRequestCommentRepository;
use Aa\ATrends\Repository\PullRequestRepository;
use DateTimeInterface;

class PullRequestCommentAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

    /**
     * @var GithubApiInterface
     */
    private $githubApi;

    /**
     * @var PullRequestRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param GithubApiInterface    $githubApi
     * @param PullRequestCommentRepository $repository
     */
    public function __construct(GithubApiInterface $githubApi, PullRequestCommentRepository $repository)
    {
        $this->githubApi = $githubApi;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(OptionsInterface $options)
    {
        $count = 0;

        // @todo: which date to choose? min from 2 tables issiea and pull request?
        $sinceDate = null; //$this->getSinceDate($this->project->getId());

        foreach ($this->githubApi->getPullRequestComments($this->project->getGithubPath(), $sinceDate) as $apiComment) {

            $comment = new PullRequestComment();

            $comment
                ->setGithubId($apiComment->getId())
                ->setPullRequestId($apiComment->getPullRequestId())
                ->setPullRequestReviewId($apiComment->getPullRequestReviewId())
                ->setGithubUserId($apiComment->getUserId())
                ->setCreatedAt($apiComment->getCreatedAt())
                ->setUpdatedAt($apiComment->getUpdatedAt());

            $this->repository->persist($comment);

            $this->progressNotifier->advance();
            $count++;

            if (0 === ($count % 10)) {
                $this->repository->flush();
            }
        }

        $this->progressNotifier->setMessage('Flushing...');
        $this->repository->flush();
    }

    /**
     * @param int $projectId
     *
     * @return DateTimeInterface
     */
    private function getSinceDate($projectId)
    {
        $lastCommitDate = $this->repository->getLastCreatedAt($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}
