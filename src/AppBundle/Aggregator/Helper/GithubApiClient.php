<?php


namespace AppBundle\Aggregator\Helper;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class GithubApiClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $authenticationToken;

    /**
     * Constructor.
     * 
     * @param ClientInterface $httpClient
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(ClientInterface $httpClient, $clientId, $clientSecret)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function authenticate()
    {
        $authBody = [
            'scopes' => ['user', 'gist'],
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $basicToken = base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret));

        $response = $this->httpClient->request('POST', 'https://api.github.com/authorizations', [
           'json' => $authBody,
            'headers' => [
                'Authorization' => sprintf('Basic %s', $basicToken)
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if (!isset($responseBody['token'])) {
            throw new \RuntimeException('Bad authorization response, no token provided');
        }

        $this->authenticationToken = $responseBody['token'];
    }

    /**
     * @param string $searchTerm
     *
     * @return array
     */
    public function findUser($searchTerm)
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/search/users', [
            'query' => [
                'q' => $searchTerm,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,

            ],
            //'headers' => ['Authorization' => sprintf('token %s', $this->authenticationToken)],
            'http_errors' => false,
        ]);

        // Unprocessed entity
        if(422 === $response->getStatusCode()) {
            return ['total_count' => 0];
        }

        // Request limit exceeded
        if(403 === $response->getStatusCode()) {
            print 's';

            $responseDate = new \DateTime($response->getHeaderLine('Date'));
            $current = $responseDate->getTimestamp();
            $reset = (int)$response->getHeaderLine('X-RateLimit-Reset');

            $timeToSleep = $reset - $current;

            sleep($timeToSleep);

            // @todo: how to protect from eternal loop?
            return $this->findUser($searchTerm);
        }

        $data = json_decode($response->getBody(), true);

        $requestLimit = $response->getHeaderLine('X-RateLimit-Remaining');

        return $data;
    }
}
