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
use AppBundle\Util\RegexUtils;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var GithubApiClient
     */
    private $apiClient;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;


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
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(GithubApiClient $apiClient,
        ProjectRepository $projectRepository,
        ContributorRepository $contributorRepository)
    {
        $this->apiClient = $apiClient;
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

        $page = 1;
        while ($commits = $this->apiClient->getCommits($projectRepo, null, $page)) {

            foreach ($commits as $commit) {

                $commitAuthorName = $commit['commit']['author']['name'];
                $commitAuthorEmail = $commit['commit']['author']['email'];

                if (isset($commit['author']['login'])) {

                    $login = $commit['author']['login'];
                    $user = $this->apiClient->getUser($login);

                    $nameMessage = '';
                    $emailMessage = '';

                    if(!isset($user['name'])) {
                        $nameMessage = "no name";
                    } else {
                        if ($user['name'] !== $commitAuthorName) {
                            $nameMessage = "User API has different name: " . $user['name'];
                        }
                    }

                    if(!isset($user['email'])) {
                        $emailMessage = "no email";
                    } else {
                        if ($user['email'] !== $commitAuthorEmail) {
                            $emailMessage = "User API has different email: " . $user['email'];
                        }
                    }

                    print sprintf('%s (%s) | %s (%s)', $commitAuthorName, $nameMessage, $commitAuthorEmail, $emailMessage).PHP_EOL;
                }
            }


            $page++;
        }
    }
}
