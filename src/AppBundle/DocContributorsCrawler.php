<?php

namespace AppBundle;

use GuzzleHttp\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class DocContributorsCrawler
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

    public function getData()
    {
        $uri = 'http://symfony.com/contributors/doc';
        $responseBody = (string)$this->getPageContents($uri);
        $domCrawler = new Crawler($responseBody, $uri);

        $contributors = [];

        $domCrawler
            ->filterXPath('//ol[position()>1]/li')

            ->each(function(Crawler $nodeCrawler) use (&$contributors) {

                $name = '';
                $nodeCrawler->filterXPath('li/text()')
                    ->each(function(Crawler $textNode) use (&$name){
                        $name .= trim($textNode->text());
                    });

                if('' === $name) {
                    $urlNode = $nodeCrawler->filterXPath('li/a');

                    $name = trim($urlNode->text());
                    $sliName = $this->getTextAfter($urlNode->attr('href'), 'https://connect.sensiolabs.com/profile/');

                    $name = sprintf('%s (%s)', $name, $sliName);
                }

                $contributors[] = $name;
            });

        return $contributors;
    }

    /**
     * @param $uri
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function getPageContents($uri)
    {
        $response = $this->httpClient->request('GET', $uri);

        $responseBody = $response->getBody();

        return $responseBody;
    }

    private function getTextAfter($text, $substring)
    {
        if (false !== ($pos = strpos($text, $substring))) {
            return substr($text, $pos + strlen($substring));
        }

        return '';
    }
}
