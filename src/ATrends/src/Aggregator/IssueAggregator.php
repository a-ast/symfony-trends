<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\Issue;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Repository\IssueRepository;
use DateTimeInterface;

class IssueAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

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
    public function aggregate(OptionsInterface $options)
    {
        $sinceDate = $this->getSinceDate($this->project->getId());

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
        $lastCommitDate = $this->repository->getLastCreatedAt($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}
