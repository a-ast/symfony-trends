<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Util\ArrayUtils;

class GithubCommitHistory implements AggregatorInterface
{
    /**
     * @var ClientAdapterInterface
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
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var array
     */
    private $maintenanceCommitPatterns;

    /**
     * Constructor.
     *
     * @param ClientAdapterInterface $apiClient
     * @param ContributorRepository $contributorRepository
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        ClientAdapterInterface $apiClient,
        ContributorRepository $contributorRepository,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository,
        array $maintenanceCommitPatterns)
    {
        $this->apiClient = $apiClient;
        $this->contributorRepository = $contributorRepository;
        $this->contributionRepository = $contributionRepository;
        $this->projectRepository = $projectRepository;
        $this->maintenanceCommitPatterns = $maintenanceCommitPatterns;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $projectId = $options['project_id'];
        /** @var Project $project */
        $project = $this->projectRepository->find($projectId);

        if (null === $project) {
            throw new \RuntimeException(sprintf('Project %d not found', $projectId));
        }

        $projectRepo = $project->getGithubPath();
        $sinceDate = $this->getSinceDate($projectId);

        foreach ($this->apiClient->getCommits($projectRepo, $sinceDate) as $commit) {
            $contributor = $this->createContributor($commit);
            $contribution = $this->createContribution($commit, $projectId, $contributor->getId());

            $this->contributionRepository->clear();
            unset($contributor);
            unset($contribution);
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
     * @param GithubCommit $commit
     * @param int $projectId
     * @param int $contributorId
     *
     * @return Contribution
     */
    private function createContribution(GithubCommit $commit, $projectId, $contributorId)
    {
        $contribution = new Contribution($projectId, $contributorId, $commit->getSha());
        $contribution->setFromGithubCommit($commit, $this->maintenanceCommitPatterns);
        $this->contributionRepository->store($contribution);

        return $contribution;
    }

    public function createContributor(GithubCommit $commit)
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
            $contributor = new Contributor($email);;
        }
        $contributor->setFromGithub($commit, $user);
        $this->contributorRepository->saveContributor($contributor);

        return $contributor;
    }

}
