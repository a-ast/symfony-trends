<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\Issue;
use Aa\ATrends\Progress\ProgressInterface;
use Aa\ATrends\Repository\IssueRepository;
use DateTimeInterface;

class IssueAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait;

    /**
     * @var GithubApiInterface
     */
    private $githubApi;
    /**
     * @var IssueRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param GithubApiInterface $githubApi
     * @param IssueRepository $repository
     */
    public function __construct(GithubApiInterface $githubApi, IssueRepository $repository)
    {
        $this->githubApi = $githubApi;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options, ProgressInterface $progress = null)
    {
        $sinceDate = $this->getSinceDate($this->project->getId());

        $progress->start();

        foreach ($this->githubApi->getIssues($this->project->getGithubPath(), $sinceDate) as $apiIssue) {

            $issue = $this->repository->findOneBy(['githubId' => $apiIssue->getId()]);
            if (null === $issue) {
                $issue = new Issue();
            }

            $issue
                ->setProjectId($this->project->getId())
                ->setGithubId($apiIssue->getId())
                ->setNumber($apiIssue->getNumber())
                ->setState($apiIssue->getState())
                ->setGithubUserId($apiIssue->getUserId())

                ->setTitle($apiIssue->getTitle())
                ->setBody($apiIssue->getBody())

                ->setCreatedAt($apiIssue->getCreatedAt())
                ->setUpdatedAt($apiIssue->getUpdatedAt())
                ->setClosedAt($apiIssue->getClosedAt())
                ->setLabels($apiIssue->getLabels())
            ;

            $this->repository->persist($issue);
            $progress->advance();
        }

        $progress->setMessage('flushing...');
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