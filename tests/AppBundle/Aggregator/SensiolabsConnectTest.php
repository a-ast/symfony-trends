<?php


namespace Tests\AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\SensiolabsDataExtractor;
use AppBundle\Aggregator\SensiolabsConnect;
use AppBundle\Client\HttpClient;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsConnectTest extends PHPUnit_Framework_TestCase
{
    public function testAggregate()
    {
        $newContributorsToPersist = [
            'gandalf' => ['country' => 'Valinor', 'city' => 'Valinor', 'github_login' => 'gandalf', 'github_url' => 'https://github.com/gandalf'],
            'frodo' => ['country' => 'Shire', 'city' => 'Shire', 'github_login' => 'frodo', 'github_url' => 'https://github.com/frodo'],
            'sam' => ['country' => 'Shire', 'city' => 'Shire', 'github_login' => 'sam', 'github_url' => 'https://github.com/sam'],
        ];

        $contributors = [];

        foreach ($newContributorsToPersist as $login => $contributorData) {

            /** @var Contributor|ObjectProphecy $contributor */
            $contributor = $this->prophesize(Contributor::class);
            $contributor
                ->getSensiolabsLogin()
                ->willReturn($login);

            $contributor
                ->setSensiolabsCountry($contributorData['country'])
                ->willReturn($contributor)
                ->shouldBeCalled();

            $contributor
                ->setSensiolabsCity($contributorData['city'])
                ->willReturn($contributor)
                ->shouldBeCalled();

            $contributor
                ->setGithubLogin($contributorData['github_login'])
                ->willReturn($contributor)
                ->shouldBeCalled();

            $contributors[] = $contributor;
        }

        $httpClient = $this->prophesize(HttpClient::class);
        $httpClient
            ->getPageDom(Argument::type('string'))
            ->willReturn(new Crawler());

        $extractor = $this->prophesize(SensiolabsDataExtractor::class);
        //foreach ($newContributorsToPersist as $contributorData) {
            $extractor
                ->extract(Argument::type(Crawler::class))
                ->willReturn($newContributorsToPersist['gandalf'], $newContributorsToPersist['frodo'], $newContributorsToPersist['sam']);
        //}

        $repository = $this->prophesize(ContributorRepository::class);

        $repository
            ->findWithSensiolabsLogin()
            ->willReturn($contributors);

        $repository
            ->flush()
            ->shouldBeCalled();

        $aggregator = new SensiolabsConnect($httpClient->reveal(), $extractor->reveal(), $repository->reveal());

        $results = $aggregator->aggregate([]);
    }
}
