<?php


namespace AppBundle\Aggregator\Helper;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
     * @param $searchType
     *
     * @return array
     */
    public function findUser($searchTerm, $searchType)
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/search/users', [
            'query' => [
                'q' => sprintf('%s in:%s type:user', $searchTerm, $searchType),
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

            $this->waitForRateLimitRecovery($response);

            // @todo: how to protect from eternal loop?
            return $this->findUser($searchTerm, $searchType);
        }

        $data = json_decode($response->getBody(), true);

        $requestLimit = $response->getHeaderLine('X-RateLimit-Remaining');

        return $data;
    }

    /**
     * @param string $userName
     *
     * @return array|bool
     */
    public function getUser($userName)
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/users/'.$userName, [
            'query' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
            'http_errors' => false,
        ]);

        // Unprocessed entity
        if(404 === $response->getStatusCode()) {
            return false;
        }

        // Unprocessed entity
        if(422 === $response->getStatusCode()) {
            throw new UnprocessableEntityHttpException($response->getReasonPhrase());
        }

        // Request limit exceeded
        if(403 === $response->getStatusCode()) {
            print 's';

            $this->waitForRateLimitRecovery($response);

            // @todo: how to protect from eternal loop?
            return $this->getUser($userName);
        }

        $data = json_decode($response->getBody(), true);

        return $data;
    }

    /**
     * @param $response
     */
    protected function waitForRateLimitRecovery($response)
    {
        $responseDate = new \DateTime($response->getHeaderLine('Date'));
        $current = $responseDate->getTimestamp();
        $reset = (int)$response->getHeaderLine('X-RateLimit-Reset');

        $timeToSleep = $reset - $current;

        sleep($timeToSleep);
    }
}
