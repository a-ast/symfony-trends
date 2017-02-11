<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\PullRequestBodyProcessor;
use Aa\ATrends\Api\Github\GithubApiInterface;
use AppBundle\Entity\PullRequest as EntityPullRequest;
use AppBundle\Helper\ProgressInterface;
use Aa\ATrends\Model\ProjectInterface;
use AppBundle\Repository\PullRequestRepository;
use DateTimeInterface;

class PullRequestAggregator implements ProjectAwareAggregatorInterface
{
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
    public function aggregate(ProjectInterface $project, array $options, ProgressInterface $progress = null)
    {
        foreach ($this->githubApi->getPullRequests($project->getGithubPath()) as $apiPullRequest) {

            $pullRequest = $this->repository->findOneBy(['githubId' => $apiPullRequest->getId()]);
            if (null === $pullRequest) {
                $pullRequest = new EntityPullRequest();
                print 'c';
            }

            $issueNumbers = $this->bodyProcessor->getIssueNumbers($apiPullRequest->getBody());

            $pullRequest
                ->setProjectId($project->getId())
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

            print '.';
            $this->repository->persist($pullRequest);
        }

        print PHP_EOL.'Flushing...'.PHP_EOL;
        $this->repository->flush();
    }

    /**
     * @param int $projectId
     *
     * @return DateTimeInterface
     */
    protected function getSinceDate($projectId)
    {
        $lastCommitDate = $this->repository->getLastCreatedAtDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}
