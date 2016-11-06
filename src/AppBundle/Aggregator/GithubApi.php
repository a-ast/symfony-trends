<?php


namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\GithubApiClient;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;

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
        $contributors = $this->repository->findAll();
        $progress->start(count($contributors));

        $report = [
            'email' => ['found' => 0, 'ambiguous' => 0],
            'login' => ['found' => 0, 'ambiguous' => 0],
            'name' => ['found' => 0, 'ambiguous' => 0],
            'errors' => []
        ];

        /** @var Contributor $contributor */
        foreach ($contributors as $contributor) {

            $progress->advance();

            foreach ($contributor->getAllEmails() as $email) {

                $usersByEmail = $this->apiClient->findUser($email);
                $searchResult = $this->processSearchResults($usersByEmail, 'email', $report);

                if($searchResult) {
                    continue(2);
                }
            }

            foreach ($contributor->getAllNames() as $name) {

                $usersByName = $this->apiClient->findUser($name);
                $searchResult = $this->processSearchResults($usersByName, 'name', $report);

                if($searchResult) {
                    continue(2);
                }
            }

            $login = $contributor->getSensiolabsLogin();

            if('' !== $login) {
                $usersByLogin = $this->apiClient->findUser($login);
                $searchResult = $this->processSearchResults($usersByLogin, 'login', $report);

                if($searchResult) {
                    continue;
                }
            }


//            $email = isset($contributor['email']) ? $contributor['email'] : '';
//            $login = $contributor['login'];
//
//            $user = $this->client->api('user')->show($login);
//            $name = isset($user['name']) ? $user['name'] : '';
//
//            if (isset($user['email'])) {
//                $email = $user['email'];
//            }


            $report['notFound'][] = sprintf('email: [%s]', $contributor->getEmail());

            print '.';
        }

        return $report;
    }





    public function processSearchResults(array $results, $typeOfSearch, array &$report)
    {
        if(1 === $results['total_count']) {
            $report[$typeOfSearch]['found']++;

            // @todo: process item or just return it
            return true;
        }

        if($results['total_count'] >= 2) {
            $report[$typeOfSearch]['ambiguous']++;
        }

        if(isset($results['error'])) {
            $report['error'][$results['error']] = true;
        }

        return false;
    }

}
