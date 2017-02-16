<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\SensiolabsDataExtractor;
use AppBundle\Repository\SensiolabsUserRepository;
use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;

class SensiolabsConnectAggregator implements AggregatorInterface
{
    use ProgressNotifierAwareTrait;

    /**
     * @var SensiolabsUserRepository
     */
    private $repository;

    /**
     * @var SensiolabsDataExtractor
     */
    private $extractor;

    /**
     * @var PageCrawlerInterface
     */
    private $pageCrawler;

    /**
     * Constructor.
     *
     * @param PageCrawlerInterface $pageCrawler
     * @param SensiolabsDataExtractor $extractor
     * @param SensiolabsUserRepository $repository
     */
    public function __construct(PageCrawlerInterface $pageCrawler,
        SensiolabsDataExtractor $extractor,
        SensiolabsUserRepository $repository)
    {
        $this->pageCrawler = $pageCrawler;
        $this->repository = $repository;
        $this->extractor = $extractor;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options)
    {
        $report = [];

        $contributors = $this->repository->findWithSensiolabsLogin();



        foreach ($contributors as $contributor) {

            $login = $contributor->getSensiolabsLogin();


            $url = $this->getProfileUrl($login);

            try {
                $crawler = $this->httpClient->getPageDom($url);
            } catch (ClientException $e) {
                $contributor->setSensiolabsPageError($e->getCode());

                continue;
            }

            $data = $this->extractor->extract($crawler);

            if('' !== $contributor->getGithubLogin() &&
                $data['github_login'] !== $contributor->getGithubLogin()
            ) {
                $report['unmatchedGuthubLogins'][] = sprintf('Id: %d, github login: [%s]',
                    $contributor->getId(), $data['github_login']);

                continue;
            }

            if ('' !== $data['github_login']) {
                $contributor->setGithubLogin($data['github_login']);
            }

            if ('' !== $data['country']) {
                $contributor->setCountry($data['country']);
            }
        }

        $this->repository->flush();

        return $report;
    }

    private function getProfileUrl($login)
    {
        return 'https://connect.sensiolabs.com/profile/'.$login;
    }


}
