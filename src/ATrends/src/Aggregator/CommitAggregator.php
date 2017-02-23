<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\Report\Report;
use Aa\ATrends\Aggregator\Report\ReportAwareInterface;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\Contribution;
use Aa\ATrends\Entity\Contributor;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Api\Github\Model\Commit as ModelGithubCommit;
use Aa\ATrends\Repository\ContributionRepository;
use Aa\ATrends\Repository\ContributorRepository;
use Aa\ATrends\Util\ArrayUtils;

class CommitAggregator implements ProjectAwareAggregatorInterface, ReportAwareInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

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
     * @var Report
     */
    private $report;

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
    public function aggregate(OptionsInterface $options)
    {
        $projectRepo = $this->project->getGithubPath();
        $sinceDate = $this->getSinceDate($this->project->getId());

        $processedRecordCount = 0;

        foreach ($this->apiClient->getCommits($projectRepo, $sinceDate) as $commit) {
            $contributor = $this->createContributor($commit);
            $this->createContribution($commit, $this->project->getId(), $contributor->getId());

            $this->progressNotifier->advance();
            $processedRecordCount++;
        }

        $this->report = $this->createReport($processedRecordCount);
    }

    /**
     * @param $projectId
     *
     * @return \DateTimeImmutable
     */
    private function getSinceDate($projectId)
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

    /**
     * @param int $processedRecordCount
     *
     * @return ReportInterface
     */
    private function createReport($processedRecordCount)
    {
        $report = new Report();
        $report->setProcessedItemCount($processedRecordCount);

        return $report;
    }

    /**
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
