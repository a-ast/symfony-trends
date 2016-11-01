<?php


namespace Tests\AppBundle\Aggregator;


use AppBundle\Aggregator\GitLog;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributionHistoryRepository;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GitLogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContributorRepository|ObjectProphecy
     */
    private $contributorRepository;

    /**
     * @var ContributionRepository|ObjectProphecy
     */
    private $contributionRepository;

    /**
     * @var ContributionHistoryRepository|ObjectProphecy
     */
    private $contributionLogRepository;

    protected function setUp()
    {
        $this->contributorRepository = $this->prophesize('AppBundle\Repository\ContributorRepository');
        $this->contributionRepository = $this->prophesize('AppBundle\Repository\ContributionRepository');
        $this->contributionLogRepository = $this->prophesize('AppBundle\Repository\ContributionHistoryRepository');
    }

    public function testAggregateOnlyIteratesContributors()
    {
        $this->contributorRepository
            ->findByEmail(Argument::type('string'))
            ->willReturn(new Contributor())
            ->shouldBeCalledTimes(7);

        $aggregator = new GitLog($this->contributorRepository->reveal(),
            $this->contributionRepository->reveal(), $this->contributionLogRepository->reveal(), __DIR__.'/fixtures/');

        $options = [
            'project_id' => 1,
        ];

        $aggregator->aggregate($options);
    }
}
