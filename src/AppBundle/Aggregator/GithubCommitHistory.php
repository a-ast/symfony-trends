<?php


namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\GeolocationApiClient;
use AppBundle\Aggregator\Helper\GithubApiClient;
use AppBundle\Entity\Contribution2;
use AppBundle\Entity\Contributor2;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\Contribution2Repository;
use AppBundle\Repository\Contributor2Repository;

class GithubCommitHistory implements AggregatorInterface
{
    /**
     * @var GithubApiClient
     */
    private $apiClient;
    /**
     * @var Contribution2Repository
     */
    private $contributionRepository;
    /**
     * @var Contributor2Repository
     */
    private $contributorRepository;
    /**
     * @var GeolocationApiClient
     */
    private $geolocationApiClient;

    /**
     * Constructor.
     *
     * @param GithubApiClient $apiClient
     * @param GeolocationApiClient $geolocationApiClient
     * @param Contribution2Repository $contributionRepository
     * @param Contributor2Repository $contributorRepository
     */
    public function __construct(GithubApiClient $apiClient, GeolocationApiClient $geolocationApiClient,
        Contribution2Repository $contributionRepository, Contributor2Repository $contributorRepository)
    {
        $this->apiClient = $apiClient;
        $this->geolocationApiClient = $geolocationApiClient;
        $this->contributionRepository = $contributionRepository;
        $this->contributorRepository = $contributorRepository;
    }


    /**
     * @inheritdoc
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $page = 1;

        $projectId = 2;
        $projectRepo = 'symfony/symfony-docs';

        while ($commits = $this->apiClient->getCommits($projectRepo, $page)) {

            foreach ($commits as $commit) {

                $contributor = null;
                $githubId = null;
                $login = '';
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
                        $countryData = $this->geolocationApiClient->findCountry($user['location']);
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
                    $contributor = new Contributor2();
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

                $contributor->addGitName($login);
                $contributor->addGitName($commitName);
                $contributor->addGitEmail($email); // in case it is not added yet
                $contributor->addGitEmail($commitEmail);

                $this->contributorRepository->flush($contributor);

                // Save contribution
                $contribution = new Contribution2();
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
