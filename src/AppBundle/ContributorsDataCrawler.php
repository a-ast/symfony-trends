<?php

namespace AppBundle;

use GuzzleHttp\ClientInterface;

class ContributorsDataCrawler
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getData($version)
    {
        $uri = sprintf('https://raw.githubusercontent.com/symfony/symfony/v%s.0/CONTRIBUTORS.md', $version);

        $response = $this->httpClient->request('GET', $uri);

        $responseBody = $response->getBody();

        $fileLines = explode(PHP_EOL, $responseBody);

        $count = 0;

        foreach ($fileLines as $line) {
            if (0 === strpos($line, ' - ')) {
                $count++;
            }
        }

        return $count;
    }
}
