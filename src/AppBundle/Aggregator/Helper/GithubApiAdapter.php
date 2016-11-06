<?php

namespace AppBundle\Aggregator\Helper;

use Github;

class GithubApiAdapter
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
     * @var integer
     */
    private $searchLimit = 0;

    /**
     * Constructor.
     *
     * @param Github\Client $client
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(
        Github\Client $client,
        $clientId,
        $clientSecret
    ) {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     *
     */
    public function authenticate()
    {
        $this->client->authenticate($this->clientId, $this->clientSecret, Github\Client::AUTH_URL_CLIENT_ID);

        $this->searchLimit = $this->client->api('rate_limit')->getSearchLimit();
    }

    /**
     * @param string $term
     *
     * @return array
     */
    public function findUser($term)
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

    /**
     * @return array
     */
    protected function getContributors($vendor, $repository)
    {
        $repoApi = $this->client->api('repo');
        $paginator = new Github\ResultPager($this->client);
        $result = $paginator->fetchAll($repoApi, 'contributors', [$vendor, $repository]);

        return $result;
    }
}
