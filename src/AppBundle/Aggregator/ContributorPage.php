<?php

namespace AppBundle\Aggregator;

use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class ContributorPage implements AggregatorInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var ContributorRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param ContributorRepository $repository
     */
    public function __construct(ClientInterface $httpClient, ContributorRepository $repository)
    {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
    }

    public function aggregate(array $options = [])
    {
        $url = 'http://symfony.com/contributors/code';

        $contributors = $this->getContributors($url);
        $contributorNamesFromPage = array_column($contributors, 0);

        // Step 1. Find doubles
        $diff = $this->checkDoubles($contributorNamesFromPage);
        foreach ($diff['page_db'] as $item) {
            print(sprintf('%s on page: %d  in db: %d',
                    $item['name'], $item['on_page'], $item['in_db'])).PHP_EOL;
        }
        print PHP_EOL;




        // Step 2. Compare DB and page
        $contributorNames = $this->repository->getContributorNames();

        $i = 0;
        foreach ($contributorNames as $contributorId => $contributor) {
            foreach ($contributor['names'] as $name) {
                if(false !== $key = $this->findArrayItem($name, $contributorNamesFromPage)) {
                    continue(2);
                }
            }

            //print sprintf('php bin/console tr:da:fix %d -o"%s <%s>" -t""', $contributorId, $contributor['names'][0], $contributor['email']).PHP_EOL;
            $i++;
        }

        print sprintf('********* Total: %d', $i);

        // Step 3. Compare page and db
        $i = 0;
        foreach ($contributorNamesFromPage as $nameFromPage) {

            $found = false;

            foreach ($contributorNames as $contributorId => $contributor) {

                if(in_array($nameFromPage, $contributor['names'])) {
                    $found = true;
                    break;
                }

            }

            if(false === $found) {
                //print sprintf('%d. %s', ++$i, $nameFromPage).PHP_EOL;
            }
        }

        print $i.PHP_EOL;

//        $missingInDb = array_diff($contributorNamesFromPage, $contributorNames);
//        $missingInPage = array_diff($contributorNames, $contributorNamesFromPage);
//
//        var_dump($missingInDb);

    }

    public function getContributors($url)
    {
        $responseBody = (string)$this->getPageContents($url);
        $domCrawler = new Crawler($responseBody, $url);

        $contributors = [];

        // @todo:
        // 1. take path of item
        // 2. try to find link and take data from it
        // 3. if not, get text

        // or try to iterate every element, try to find order


        $domCrawler
            ->filterXPath('//ol[position()>1]/li')
            //->filterXPath(sprintf('//ol[position*(>1]/li', $blockPositionCondition))
            ->each(function(Crawler $nodeCrawler) use (&$contributors) {

                $name = '';
                $sliName = '';
                $nodeCrawler->filterXPath('li/text()')
                    ->each(function(Crawler $textNode) use (&$name){
                        $name .= trim($textNode->text());
                    });

                if('' === $name) {
                    $urlNode = $nodeCrawler->filterXPath('li/a');

                    $name = trim($urlNode->text());
                    $sliName = $this->getTextAfter($urlNode->attr('href'), 'https://connect.sensiolabs.com/profile/');

                }

                $contributors[] = [$name, $sliName];
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

    /**
     * @param $contributorNames
     *
     * @return array
     */
    protected function checkDoubles($contributorNames)
    {
        $frequencyOnPage = array_count_values($contributorNames);
        $doublesOnPage = array_filter($frequencyOnPage, function($frequency) {
            return $frequency > 0;
        });
        ksort($doublesOnPage);

        $doublesInDb = $this->repository->getDoubles();

        $diff = [];

        foreach ($doublesInDb as $name => $count) {
            if(!isset($doublesOnPage[$name]) || $doublesOnPage[$name] !== $count) {
                $diff['db_page'][] = [
                    'name' => $name,
                    'on_page' => isset($doublesOnPage[$name]) ? $doublesOnPage[$name] : 0,
                    'in_db' => $count,
                ];
            }
        }

        foreach ($doublesOnPage as $name => $count) {
            if(!isset($doublesInDb[$name]) || $doublesInDb[$name] !== $count) {
                $diff['page_db'][] = [
                    'name' => $name,
                    'in_db' => isset($doublesInDb[$name]) ? $doublesInDb[$name] : 0,
                    'on_page' => $count,
                ];
            }
        }

        return $diff;
    }

    /**
     * @param mixed $item
     * @param array $array
     *
     * @return mixed
     */
    protected function findArrayItem($item, array &$array)
    {
        return array_search(strtolower($item), array_map('strtolower', $array));
    }
}
