<?php


namespace AppBundle\Aggregator;

use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepository;
use Github;

class GithubApi implements AggregatorInterface
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var Github\Client
     */
    private $client;

    /**
     * @var ContributorRepository
     */
    private $repository;

    /**
     * @var integer
     */
    private $searchLimit = 0;

    /**
     * Constructor.
     *
     * @param Github\Client $client
     * @param ContributorRepository $repository
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(
        Github\Client $client,
        ContributorRepository $repository,
        $clientId,
        $clientSecret
    ) {
        $this->client = $client;
        $this->repository = $repository;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    /**
     * @param array $options
     *
     * @return array
     */
    public function aggregate(array $options = [])
    {
        $this->authenticate();

        $this->searchLimit = $this->client->api('rate_limit')->getSearchLimit();

        // @todo: replace to email and logins and names only
        $contributors = $this->repository->findAll();

        $report = [
            'email' => ['found' => 0, 'ambiguous' => 0],
            'login' => ['found' => 0, 'ambiguous' => 0],
            'name' => ['found' => 0, 'ambiguous' => 0],
            'errors' => []
        ];

        /** @var Contributor $contributor */
        foreach ($contributors as $contributor) {

            foreach ($contributor->getGitEmails() as $email) {
                $usersByEmail = $this->findUser($email);
                $searchResult = $this->processSearchResults($usersByEmail, 'email', $report);

                if($searchResult) {
                    continue;
                }
            }

            foreach ($contributor->getGitNames() as $name) {
                $usersByName = $this->findUser($name);
                $searchResult = $this->processSearchResults($usersByName, 'name', $report);
            }

            $usersByLogin = $this->findUser($contributor->getSensiolabsLogin());
            $searchResult = $this->processSearchResults($usersByLogin, 'login', $report);

            // @todo: search by all names


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

    /**
     * @return array
     */
    protected function getContributors()
    {
        $repoApi = $this->client->api('repo');
        $paginator = new Github\ResultPager($this->client);
        $result = $paginator->fetchAll($repoApi, 'contributors', ['symfony', 'symfony']);

        return $result;
    }

    protected function authenticate()
    {
        $this->client->authenticate($this->clientId, $this->clientSecret, Github\Client::AUTH_URL_CLIENT_ID);
    }

    /**
     * @param string $term
     *
     * @return array
     */
    protected function findUser($term)
    {
        if(0 === $this->searchLimit) {
            do {
                sleep(10);
                print 's';
                $this->searchLimit = $this->client->api('rate_limit')->getSearchLimit();

            } while (0 === $this->searchLimit);
        }


        try {
            $results = $this->client->api('search')->users($term);
        } catch (\Exception $exception) {
            $results = [
                'total_count' => 0,
                'error' => $exception->getMessage(),
            ];
        }

        $this->searchLimit--;

        return $results;
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
