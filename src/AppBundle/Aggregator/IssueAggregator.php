<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\Issue;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\ProjectInterface;
use AppBundle\Repository\IssueRepository;
use DateTimeInterface;

class IssueAggregator implements ProjectAwareAggregatorInterface
{
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
    public function aggregate(ProjectInterface $project, array $options, ProgressInterface $progress = null)
    {
        $sinceDate = $this->getSinceDate($project->getId());

        foreach ($this->githubApi->getIssues($project->getGithubPath(), $sinceDate) as $apiIssue) {

            $issue = $this->repository->findOneBy(['githubId' => $apiIssue->getId()]);
            if (null === $issue) {
                $issue = new Issue();
                print 'c';
            }

            $issue
                ->setProjectId($project->getId())
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

            print '.';
            $this->repository->persist($issue);
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
        $lastCommitDate = $this->repository->getLastCreatedAt($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}
