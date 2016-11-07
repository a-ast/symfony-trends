<?php


namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\GithubApiClient;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Util\StringUtils;

class GithubApi implements AggregatorInterface
{

    const PROFILE_URL = 'https://github.com/';

    /**
     * @var ContributorRepository
     */
    private $repository;
    /**
     * @var GithubApiClient
     */
    private $apiClient;

    /**
     * Constructor.
     *
     * @param GithubApiClient $apiClient
     * @param ContributorRepository $repository
     */
    public function __construct(
        GithubApiClient $apiClient,
        ContributorRepository $repository
    ) {
        $this->repository = $repository;
        $this->apiClient = $apiClient;
    }


    /**
     * @param array $options
     *
     * @param ProgressInterface $progress
     *
     * @return array
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        // @todo: replace to get records with emails, logins and names only
        $contributors = $this->repository->findWithoutGithubLogin(300);
        $progress->start(count($contributors));

        $report = [
            'notFoundCount' => 0,
        ];

        /** @var Contributor $contributor */
        foreach ($contributors as $contributor) {

            $progress->advance();

            foreach ($contributor->getAllEmails() as $email) {

                $usersByEmail = $this->apiClient->findUser($email, 'email');
                $searchResult = $this->processSearchResults($email, $usersByEmail, 'email', $report);

                if(false !== $searchResult) {

                    $contributor->setGithubLogin($searchResult);

                    continue(2);
                }
            }

            foreach ($contributor->getAllNames() as $name) {
                // if name is not complex (only one word), skip it
                if(!StringUtils::contains($name, ' ')) {
                    continue;
                }

                $usersByName = $this->apiClient->findUser($name, 'fullname');
                $searchResult = $this->processSearchResults($name, $usersByName, 'fullname', $report);

                if(false !== $searchResult) {

                    if (in_array($searchResult, $contributor->getAllNames())) {
                        $contributor->setGithubLogin($searchResult);
                    } else {
                        $report['fullname']['doubtful'][$contributor->getEmail()] = sprintf('found github: %s, sensiolabs: %s', $searchResult, $contributor->getSensiolabsLogin());

                        // @todo: write full answer from API to compare visually
                    }

                    continue(2);
                }
            }


            foreach ($contributor->getAllNames() as $knownName) {

                $userByLogin = $this->apiClient->getUser($knownName);

                if(false !== $userByLogin) {

                    if(in_array($userByLogin['name'], $contributor->getAllNames())) {
                        $report['login']['found'] = sprintf('found github login: %s, sensiolabs login: %s, github name %s, stored name: %s',
                            $userByLogin['login'], $contributor->getSensiolabsLogin(), $userByLogin['name'], $contributor->getName());
                    }

                    // @todo: write full answer from API to compare visually
                }

            }

            $report['notFoundCount']++;

        }

        $this->repository->flush();

        return $report;
    }

    public function processSearchResults($searchTerm, array $results, $typeOfSearch, array &$report)
    {
        if(1 === $results['total_count']) {
            return $results['items'][0]['login'];
        }

        if($results['total_count'] >= 2) {
            foreach ($results['items'] as $item) {
                if($searchTerm === $item['login']) {
                    return $item['login'];
                }
            }
        }

        if(isset($results['error'])) {
            $report['error'][$results['error']] = true;
        }

        return false;
    }

}
