<?php


namespace AppBundle\Aggregator;

use AppBundle\Client\GeolocationApiClient;
use AppBundle\Client\GithubApiClient;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ProjectRepository;

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
     * @var GeolocationApiClient
     */
    private $geolocationApiClient;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * Constructor.
     *
     * @param GithubApiClient $apiClient
     * @param GeolocationApiClient $geolocationApiClient
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param ContributorRepository $contributorRepository
     */
    public function __construct(GithubApiClient $apiClient,
        GeolocationApiClient $geolocationApiClient,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository, ContributorRepository $contributorRepository)
    {
        $this->apiClient = $apiClient;
        $this->geolocationApiClient = $geolocationApiClient;
        $this->contributionRepository = $contributionRepository;
        $this->contributorRepository = $contributorRepository;
        $this->projectRepository = $projectRepository;
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

        $lastCommitDate = $this->contributionRepository->getLastCommitDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        $page = 1;
        while ($commits = $this->apiClient->getCommits($projectRepo, $sinceDate, $page)) {

            foreach ($commits as $commit) {

                $contributor = null;
                $githubId = null;
                $login = '';
                $location = '';
                $country = '';

                $name  = $commitName  = $commit['commit']['author']['name'];
                $email = $commitEmail = $commit['commit']['author']['email'];

                if (null !== $commit['author'] && isset($commit['author']['id'])) {
                    $githubId = $commit['author']['id'];
                    $contributor = $this->contributorRepository->findByGithubId($githubId);

                    if (null !== $contributor) {
                        print '.';
                    }
                }

                // if contributor is not found or github id is not set,
                // but login is present
                if ((null === $contributor || null === $contributor->getGithubId()) &&
                    null !== $commit['author'] && isset($commit['author']['login'])) {

                    $login = $commit['author']['login'];
                    $user = $this->apiClient->getUser($login);

                    $name = isset($user['name']) ? $user['name'] : $commitName;
                    $email = isset($user['email']) ? $user['email'] : $commitEmail;

                    if (isset($user['location'])) {
                        $location = $user['location'];
                        $countryData = $this->geolocationApiClient->findCountry($location);
                        $country = $countryData['country'];
                    }
                }

                // if contributor not found by id, try to find by email
                if (null === $contributor) {
                    $contributor = $this->contributorRepository->findByEmail($email);

                    if (null !== $contributor) {
                        print 'e';
                    }
                }

                if (null === $contributor) {
                    $contributor = new Contributor();
                    $contributor
                        ->setEmail($email)
                        ->setGitEmails([])
                        ->setGitNames([])
                        ->setCreatedAt(new \DateTime())
                        ->setUpdatedAt(new \DateTime())
                    ;

                    $this->contributorRepository->persist($contributor);

                    print 'n';
                }

                if (!$contributor->getGithubLogin()) {
                    $contributor->setGithubLogin($login);
                }

                if (!$contributor->getGithubId()) {
                    $contributor->setGithubId($githubId);
                }

                if (!$contributor->getName()) {
                    $contributor->setName($name);
                }

                if (!$contributor->getCountry()) {
                    $contributor->setCountry($country);
                }

                if (!$contributor->getGithubLocation()) {
                    $contributor->setGithubLocation($location);
                }

                $contributor->addGitName($login);
                $contributor->addGitName($commitName);
                $contributor->addGitEmail($email); // in case it is not added yet
                $contributor->addGitEmail($commitEmail);

                $this->contributorRepository->flush($contributor);

                // Save contribution
                $contribution = new Contribution();
                $contribution
                    ->setProjectId($projectId)
                    ->setContributorId($contributor->getId())
                    ->setCommitHash($commit['sha'])
                    ->setCommitedAt(new \DateTime($commit['commit']['author']['date']))
                ;

                $this->contributionRepository->persist($contribution);
                $this->contributionRepository->flush($contribution);
                $this->contributionRepository->clear();

                unset($contributor);
                unset($contribution);
            }

            $page++;
        }
    }
}
