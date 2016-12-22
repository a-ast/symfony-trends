<?php


namespace AppBundle\Aggregator;

use AppBundle\Client\ApiFacade;
use AppBundle\Client\GithubApiClient;
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
     * @var GithubApiClient
     */
    private $apiClient;

    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var array
     */
    private $maintenanceCommitPatterns;

    /**
     * @var ApiFacade
     */
    private $apiFacade;

    /**
     * Constructor.
     *
     * @param ApiFacade $apiFacade
     * @param GithubApiClient $apiClient
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param ContributorRepository $contributorRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        ApiFacade $apiFacade,
        GithubApiClient $apiClient,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository,
        ContributorRepository $contributorRepository,
        array $maintenanceCommitPatterns)
    {
        $this->apiClient = $apiClient;
        $this->contributionRepository = $contributionRepository;
        $this->contributorRepository = $contributorRepository;
        $this->projectRepository = $projectRepository;
        $this->maintenanceCommitPatterns = $maintenanceCommitPatterns;
        $this->apiFacade = $apiFacade;
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

        $page = 1;

        while ($commits = $this->apiClient->getCommits($projectRepo, $sinceDate, $page)) {

            foreach ($commits as $commitData) {

                $commit = new GithubCommit($commitData);

                $contributor = $this->createContributor($commit);
                $contribution = $this->createContribution($commit, $projectId, $contributor->getId());

                $this->contributionRepository->clear();
                unset($contributor);
                unset($contribution);
            }

            $page++;
        }
    }

    /**
     * @param $projectId
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
     *
     * @return Contributor
     */
    private function createContributor(GithubCommit $commit)
    {
        $contributor = null;
        $user = null;
        $userEmails = [$commit->getCommitterEmail()];

        if (null !== $commit->getCommitterId()) {
            $contributor = $this->contributorRepository->findByGithubId($commit->getCommitterId());
        }

        // if contributor is not found or github id is not set,
        // but login is present
        if ((null === $contributor || null === $contributor->getGithubId()) && '' !== $commit->getCommitterLogin()) {
            $user = $this->apiFacade->getGithubUserWithLocation($commit->getCommitterLogin());
            array_unshift($userEmails, $user->getEmail());
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
        $this->contributorRepository->addContributor($contributor);

        return $contributor;
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
        $contribution = new Contribution($projectId, $contributorId, $commit->getHash());
        $contribution->setFromGithubCommit($commit, $this->maintenanceCommitPatterns);
        $this->contributionRepository->store($contribution);

        return $contribution;
    }
}
