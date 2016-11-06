<?php


namespace Tests\AppBundle\Aggregator;

use AppBundle\Aggregator\SensiolabsConnect;
use AppBundle\Client\HttpClient;
use AppBundle\Repository\ContributorRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

class SensiolabsConnectTest extends PHPUnit_Framework_TestCase
{
    public function testAggregate()
    {
        $httpClient = $this->prophesize(HttpClient::class);
        $repository = $this->prophesize(ContributorRepository::class);

        $repository
            ->findWithSensiolabsLogin()
            ->willReturn(['gandalf', 'frodo', 'sam'])
        ;

        $newContributorsToPersist = [
            'gandalf' => ['country' => 'Valinor', 'city' => 'Valinor', 'github' => 'gandalf'],
            'frodo' => ['country' => 'Shire', 'city' => 'Shire', 'github' => 'frodo'],
            'sam' => ['country' => 'Shire', 'city' => 'Shire', 'github' => 'sam'],
        ];

        foreach ($newContributorsToPersist as $contributor) {
            $repository
                ->persist(Argument::which('getCountry', $contributor['country']))
                ->shouldBeCalled()
            ;

            $repository
                ->persist(Argument::which('getCity', $contributor['city']))
                ->shouldBeCalled()
            ;

            $repository
                ->persist(Argument::which('getGithubLogin', $contributor['github']))
                ->shouldBeCalled()
            ;
        }

        $repository
            ->flush()
            ->shouldBeCalled()
        ;

        $aggregator = new SensiolabsConnect($httpClient->reveal(), $repository->reveal());

        $results = $aggregator->aggregate([]);
    }
}
