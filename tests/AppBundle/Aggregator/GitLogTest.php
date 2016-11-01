<?php


namespace Tests\AppBundle\Aggregator;


use AppBundle\Aggregator\GitLog;
use AppBundle\Entity\Contributor;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

class GitLogTest extends PHPUnit_Framework_TestCase
{
    public function testAggregateOnlyIteratesContributors()
    {
        $contributorRepository = $this->prophesize('AppBundle\Repository\ContributorRepository');

        $contributorRepository
            ->findByEmail(Argument::type('string'))
            ->willReturn(new Contributor())
            ->shouldBeCalledTimes(7);

        $contributionRepository = $this->prophesize('AppBundle\Repository\ContributionRepository');
        $contributionLogRepository = $this->prophesize('AppBundle\Repository\ContributionHistoryRepository');

        $aggregator = new GitLog($contributorRepository->reveal(),
            $contributionRepository->reveal(), $contributionLogRepository->reveal(), __DIR__.'/fixtures/');

        $options = [
            'project_id' => 1,
        ];

        $aggregator->aggregate($options);
    }
}
