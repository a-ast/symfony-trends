<?php


namespace Tests\AppBundle\Aggregator;


use AppBundle\Aggregator\GithubApi;
use AppBundle\Aggregator\Helper\GithubApiAdapter;
use AppBundle\Repository\ContributorRepository;
use PHPUnit_Framework_TestCase;

class GithubApiTest extends PHPUnit_Framework_TestCase
{

    public function testAggregeate()
    {
        $apiAdapter = $this->prophesize(GithubApiAdapter::class);
        $apiAdapter
            ->authenticate()
            ->shouldBeCalled();

        $repository = $this->prophesize(ContributorRepository::class);

        $aggregator = new GithubApi($apiAdapter, $repository);

        $result = $aggregator->aggregate([]);


    }
}
