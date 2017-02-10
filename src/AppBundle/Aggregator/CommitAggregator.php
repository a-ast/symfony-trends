<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\GithubCommit as ModelGithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Util\ArrayUtils;

class CommitAggregator implements ProjectAwareAggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $apiClient;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @var array
     */
    private $maintenanceCommitPatterns;

    /**
     * Constructor.
     *
     * @param GithubApiInterface $apiClient
     * @param ContributorRepository $contributorRepository
     * @param ContributionRepository $contributionRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        GithubApiInterface $apiClient,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        array $maintenanceCommitPatterns)
    {
        $this->apiClient = $apiClient;
        $this->contributorRepository = $contributorRepository;
        $this->contributionRepository = $contributionRepository;
        $this->maintenanceCommitPatterns = $maintenanceCommitPatterns;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $projectRepo = $project->getGithubPath();
        $sinceDate = $this->getSinceDate($project->getId());

        foreach ($this->apiClient->getCommits($projectRepo, $sinceDate) as $commit) {
            $contributor = $this->createContributor($commit);
            $this->createContribution($commit, $project->getId(), $contributor->getId());
        }
    }

    /**
     * @param $projectId
     *
     * @return \DateTimeImmutable
     */
    protected function getSinceDate($projectId)
    {
        $lastCommitDate = $this->contributionRepository->getLastCommitDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }

    /**
     * @param ModelGithubCommit $commit
     *
     * @return Contributor
     */
    private function createContributor(ModelGithubCommit $commit)
    {
        $contributor = null;
        $user = null;
        $userEmails = [$commit->getCommitAuthorEmail()];

        if (null !== $commit->getAuthorId()) {
            $contributor = $this->contributorRepository->findByGithubId($commit->getAuthorId());
        }

        // if contributor is not found or github id is not set,
        // but login is present
        if ((null === $contributor || null === $contributor->getGithubId()) && '' !== $commit->getAuthorLogin()) {
            $user = $this->apiClient->getUser($commit->getAuthorLogin());

            if (null !== $user) {
                array_unshift($userEmails, $user->getEmail());
            }
        }

        // if contributor not found by id, try to find it by email
        if (null === $contributor) {
            $contributor = $this->contributorRepository->findByEmails($userEmails);
        }

        $email = ArrayUtils::getFirstNonEmptyElement($userEmails);

        if (null === $contributor) {
            $contributor = new Contributor($email);
        }
        $contributor->setFromGithub($commit, $user);
        $this->contributorRepository->saveContributor($contributor);

        return $contributor;
    }

    /**
     * @param ModelGithubCommit $commit
     * @param int $projectId
     * @param int $contributorId
     *
     * @return Contribution
     */
    private function createContribution(ModelGithubCommit $commit, $projectId, $contributorId)
    {
        $contribution = new Contribution($projectId, $contributorId, $commit->getSha());
        $contribution->setFromGithubCommit($commit, $this->maintenanceCommitPatterns);
        $this->contributionRepository->store($contribution);

        return $contribution;
    }
}
