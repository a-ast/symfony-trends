<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Progress\ProgressNotifierInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\PullRequest as EntityPullRequest;
use Aa\ATrends\Repository\PullRequestRepository;
use DateTimeInterface;

class PullRequestAggregator implements ProjectAwareAggregatorInterface
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
     * @var PullRequestBodyProcessor
     */
    private $bodyProcessor;

    /**
     * Constructor.
     *
     * @param GithubApiInterface $githubApi
     * @param PullRequestRepository $repository
     * @param PullRequestBodyProcessor $bodyProcessor
     */
    public function __construct(GithubApiInterface $githubApi, PullRequestRepository $repository, PullRequestBodyProcessor $bodyProcessor)
    {
        $this->githubApi = $githubApi;
        $this->repository = $repository;
        $this->bodyProcessor = $bodyProcessor;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options)
    {
        foreach ($this->githubApi->getPullRequests($this->project->getGithubPath()) as $apiPullRequest) {

            $pullRequest = $this->repository->findOneBy(['githubId' => $apiPullRequest->getId()]);
            if (null === $pullRequest) {
                $pullRequest = new EntityPullRequest();
            }

            $issueNumbers = $this->bodyProcessor->getIssueNumbers($apiPullRequest->getBody());

            $pullRequest
                ->setProjectId($this->project->getId())
                ->setGithubId($apiPullRequest->getId())
                ->setNumber($apiPullRequest->getNumber())
                ->setState($apiPullRequest->getState())
                ->setGithubUserId($apiPullRequest->getUserId())

                ->setTitle($apiPullRequest->getTitle())
                ->setBody($apiPullRequest->getBody())

                ->setCreatedAt($apiPullRequest->getCreatedAt())
                ->setUpdatedAt($apiPullRequest->getUpdatedAt())
                ->setClosedAt($apiPullRequest->getClosedAt())
                ->setMergedAt($apiPullRequest->getMergedAt())

                ->setMergeSha($apiPullRequest->getMergeSha())
                ->setHeadSha($apiPullRequest->getHeadSha())
                ->setBaseSha($apiPullRequest->getBaseSha())

                ->setBaseRef($apiPullRequest->getBaseRef())

                ->setIssueNumbers($issueNumbers)
            ;

            $this->repository->persist($pullRequest);
            $this->progressNotifier->advance();
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
        $lastCommitDate = $this->repository->getLastCreatedAtDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}
