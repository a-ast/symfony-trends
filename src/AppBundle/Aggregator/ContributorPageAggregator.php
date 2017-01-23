<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\ContributorExtractor;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\ClientInterface;

class ContributorPageAggregator implements AggregatorInterface
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
     * @var ContributorExtractor
     */
    private $extractor;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param ContributorExtractor $extractor
     * @param ContributorRepository $repository
     */
    public function __construct(
        ClientInterface $httpClient,
        ContributorExtractor $extractor,
        ContributorRepository $repository
    ) {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
        $this->extractor = $extractor;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $url = $options['url'];

        $contributors = $this->getContributors($url);

        $i = 0;
        foreach ($contributors as $contributor) {
            if ('' !== $contributor['sensiolabs_url']) {
                $i++;
            }
        }

        $notFoundNames = [];
        $ambiguousNames = [];
        $unmatchedNames = [];
        $processedCount = 0;

        foreach ($contributors as $contributor) {
            if ('' === $contributor['sensiolabs_url']) {
                continue;
            }

            $name = $contributor['name'];
            $sensiolabsLogin = $this->getSensiolabsLoginFromUrl($contributor['sensiolabs_url']);

            $existingContributors = $this->repository->findByName($name);

            if (0 === count($existingContributors)) {
                $notFoundNames[] = $name;

                continue;
            }

            $existingContributors = array_filter($existingContributors,
                function(Contributor $contributor) use ($sensiolabsLogin){
                    return $sensiolabsLogin !== $contributor->getSensiolabsLogin();
                });

            if (count($existingContributors) > 1) {

                $ambiguousNames[] = $name;

                continue;
            }

            if (0 === count($existingContributors)) {
                continue;
            }

            $existingContributor = current($existingContributors);

            if ('' !== $existingContributor->getSensiolabsLogin()) {

                if ($sensiolabsLogin !== $existingContributor->getSensiolabsLogin()) {
                    $unmatchedNames[] = $name;
                }

                continue;
            }

            $existingContributor->setSensiolabsLogin($sensiolabsLogin);
            $processedCount++;
        }

        $this->repository->flush();

        return [
            'Not found names' => $notFoundNames,
            'Ambiguous names' => $ambiguousNames,
            'Unmatched names' => $unmatchedNames,
            'Processed contributors' => $processedCount,
        ];
    }

    protected function getContributors($url)
    {
        $responseBody = $this->getPageContents($url);

        $contributors = $this->extractor->extract($responseBody);

        return $contributors;
    }

    /**
     * @param $uri
     * @return string
     */
    protected function getPageContents($uri)
    {
        $response = $this->httpClient->request('GET', $uri);

        $responseBody = $response->getBody();

        return (string)$responseBody;
    }

    /**
     * @param $contributorNamesFromPage
     */
    protected function analyzeNames($contributorNamesFromPage)
    {
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
                if (false !== $this->findArrayItem($name, $contributorNamesFromPage)) {
                    continue(2);
                }
            }

            $i++;
        }

        print sprintf('********* Total: %d', $i);

        // Step 3. Compare page and db
        $i = 0;
        foreach ($contributorNamesFromPage as $nameFromPage) {

            $found = false;

            foreach ($contributorNames as $contributorId => $contributor) {

                if (in_array($nameFromPage, $contributor['names'])) {
                    $found = true;
                    break;
                }

            }
        }

        print $i.PHP_EOL;
    }

    /**
     * @param $contributorNames
     *
     * @return array
     */
    protected function checkDoubles($contributorNames)
    {
        $frequencyOnPage = array_count_values($contributorNames);
        $doublesOnPage = array_filter($frequencyOnPage, function ($frequency) {
            return $frequency > 0;
        });
        ksort($doublesOnPage);

        $doublesInDb = $this->repository->getDoubles();

        $diff = [];

        foreach ($doublesInDb as $name => $count) {
            if (!isset($doublesOnPage[$name]) || $doublesOnPage[$name] !== $count) {
                $diff['db_page'][] = [
                    'name' => $name,
                    'on_page' => isset($doublesOnPage[$name]) ? $doublesOnPage[$name] : 0,
                    'in_db' => $count,
                ];
            }
        }

        foreach ($doublesOnPage as $name => $count) {
            if (!isset($doublesInDb[$name]) || $doublesInDb[$name] !== $count) {
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

    private function getTextAfter($text, $substring)
    {
        if (false !== ($pos = strpos($text, $substring))) {
            return substr($text, $pos + strlen($substring));
        }

        return '';
    }

    private function getSensiolabsLoginFromUrl($url)
    {
        return $this->getTextAfter($url, 'https://connect.sensiolabs.com/profile/');
    }

}
