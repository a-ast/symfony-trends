<?php


namespace Tests\AppBundle\Aggregator;


use AppBundle\Aggregator\GitLog;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributionHistoryRepository;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
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

    public function testOnlyIteratesContributors()
    {
        $options = [
            'project_id' => 1,
        ];
        
        $this->contributorRepository
            ->findByEmail(Argument::type('string'))
            ->willReturn(new Contributor())
            ->shouldBeCalledTimes(7);

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }

    public function testOnlyIteratesContributorsSinceGivenDate()
    {
        $options = [
            'project_id' => 1,
            'since_datetime' => '2010-01-04T19:00:00+01:00'
        ];
        
        $this->contributorRepository
            ->findByEmail(Argument::type('string'))
            ->willReturn(new Contributor())
            ->shouldBeCalledTimes(3);

        $aggregator = $this->getGitLog();
        
        $aggregator->aggregate($options);
    }

    public function testCreateNewContributors()
    {
        $options = [
            'project_id' => 1,
            'update_contributors' => true,
        ];

        $this->contributorRepository
            ->findByEmail(Argument::type('string'))
            ->will(function ($args) {
                $c = ('fb@email.email' == $args[0]) ? new Contributor() : null;

                return $c;
            });

        $newContributors = [
            'Fabian Lange' => 'fl@email.email',
            'pborreli' => 'pb@email.email',
            'Dennis Benkert' => 'db@email.email',
        ];

        foreach ($newContributors as $name => $email) {
            $this->contributorRepository
                ->persist(Argument::which('getEmail', $email))
                ->shouldBeCalled();

            $this->contributorRepository
                ->persist(Argument::which('getName', $name))
                ->shouldBeCalled();
        }

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }
    
    
    private function getGitLog()
    {
        return new GitLog($this->contributorRepository->reveal(),
            $this->contributionRepository->reveal(), $this->contributionLogRepository->reveal(), __DIR__.'/fixtures/');
    }
}
