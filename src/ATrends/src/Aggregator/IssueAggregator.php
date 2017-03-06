<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Api\Github\Model\Issue as ApiIssue;
use Aa\ATrends\Entity\Issue;
use Aa\ATrends\Entity\PullRequest;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Repository\IssueRepository;
use Aa\ATrends\Repository\PullRequestRepository;
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
    private $issueRepository;

    /**
     * @var PullRequestRepository
     */
    private $pullRequestRepository;

    /**
     * Constructor.
     *
     * @param GithubApiInterface    $githubApi
     * @param IssueRepository       $issueRepository
     * @param PullRequestRepository $pullRequestRepository
     */
    public function __construct(GithubApiInterface $githubApi, IssueRepository $issueRepository, PullRequestRepository $pullRequestRepository)
    {
        $this->githubApi = $githubApi;
        $this->issueRepository = $issueRepository;
        $this->pullRequestRepository = $pullRequestRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(OptionsInterface $options)
    {
        // @todo: which date to choose? min from 2 tables?
        $sinceDate = null; $this->getSinceDate($this->project->getId());

        foreach ($this->githubApi->getIssues($this->project->getGithubPath(), $sinceDate) as $apiIssue) {

            if ($apiIssue->isPullRequest()) {
                $issue = $this->findOrCreatePullRequest($apiIssue);
            } else {
                $issue = $this->findOrCreateIssue($apiIssue);
            }

            $issue
                ->setProjectId($this->project->getId())
                // @todo: does it make sense to set githubid which is wrong???
                //->setGithubId($apiIssue->getId())
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

            $this->issueRepository->persist($issue);

            $this->progressNotifier->advance();
        }

        $this->progressNotifier->setMessage('Flushing...');
        $this->issueRepository->flush();
    }

    /**
     * @param int $projectId
     *
     * @return DateTimeInterface
     */
    private function getSinceDate($projectId)
    {
        $lastCommitDate = $this->issueRepository->getLastCreatedAt($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }

    /**
     * @param ApiIssue $apiIssue
     *
     * @return Issue
     */
    private function findOrCreateIssue(ApiIssue $apiIssue)
    {
        $issue = $this->issueRepository->findOneBy(['number' => $apiIssue->getNumber(), 'projectId' => $this->getProject()->getId()]);
        if (null === $issue) {
            $issue = new Issue();
            $issue->setGithubId($apiIssue->getId());
        }

        return $issue;
    }

    /**
     * @param ApiIssue $apiIssue
     *
     * @return PullRequest
     */
    private function findOrCreatePullRequest(ApiIssue $apiIssue)
    {
        $pullRequest = $this->pullRequestRepository->findOneBy(['number' => $apiIssue->getNumber(), 'projectId' => $this->getProject()->getId()]);
        if (null === $pullRequest) {
            $pullRequest = new PullRequest();
            $pullRequest
                ->setGithubId(0)
                ->setBaseRef('')
                ->setMergeSha('')
                ->setHeadSha('')
                ->setBaseSha('')
            ;
        }

        return $pullRequest;
    }
}
