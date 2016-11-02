<?php


namespace tests\AppBundle\Aggregator;


use AppBundle\Aggregator\ContributorPage;
use AppBundle\Aggregator\Helper\ContributorExtractor;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepository;
use Guzzle\Http\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class ContributorPageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContributorRepository|ObjectProphecy
     */
    private $repository;

    /**
     * @var ClientInterface|ObjectProphecy
     */
    private $httpClient;
    private $extractor;

    /**
     * @var ContributorPage
     */
    private $aggregator;

    protected function setUp()
    {
        $this->httpClient = $this->prophesize(Client::class);
        $this->httpClient
            ->request('GET', Argument::type('string'))
            ->willReturn($this->prophesize(Response::class));

        $this->repository = $this->prophesize(ContributorRepository::class);
        $this->extractor = $this->prophesize(ContributorExtractor::class);

        $this->aggregator = new ContributorPage($this->httpClient->reveal(), $this->extractor->reveal(), $this->repository->reveal());
    }

    public function testDryRun()
    {
        $extractedContributors = [
            ['name' => 'Gandalf', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/gandalf'],
            ['name' => 'Frodo Baggins', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/frodo'],
            ['name' => 'Samwise Gamgee', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/sam'],
            ['name' => 'Meriadoc Brandybuck', 'sensiolabs_url' => ''],
            ['name' => 'Legolas', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/legolas'],
        ];

        $this->extractor
            ->extract(Argument::type('string'))
            ->willReturn($extractedContributors);

        $this->repository
            ->findByName('Gandalf')
            ->willReturn([]);

        $this->repository
            ->findByName('Frodo Baggins')
            ->willReturn([new Contributor(), new Contributor()]);

        $this->repository
            ->findByName('Samwise Gamgee')
            ->willReturn([$this->getContributor('banazir')]);

        $this->repository
            ->findByName('Legolas')
            ->willReturn([new Contributor()]);

        $this->repository
            ->flush()
            ->shouldBeCalledTimes(1);

        $expectedResult = [
            'Not found names' => ['Gandalf'],
            'Ambiguous names' => ['Frodo Baggins'],
            'Unmatched names' => ['Samwise Gamgee'],
            'Processed contributors' => 1,
        ];

        $result = $this->aggregator->aggregate(['url' => 'middle-earth']);

        $this->assertEquals($expectedResult, $result);
    }

    private function getContributor($sensiolabsLogin = '')
    {
        $contributor = new Contributor();

        return $contributor->setSensiolabsLogin($sensiolabsLogin);
    }
}
