<?php

namespace Tests\AppBundle\Aggregator;

use AppBundle\Aggregator\GitLog;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\ContributionLog;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepositoryFacade;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GitLogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContributorRepositoryFacade|ObjectProphecy
     */
    private $repositoryFacade;

    protected function setUp()
    {
        $this->repositoryFacade = $this->prophesize('AppBundle\Repository\ContributorRepositoryFacade');
    }

    public function testOnlyIteratesContributors()
    {
        $options = [
            'project_id' => 1,
        ];
        
        $this->repositoryFacade
            ->findContributorByEmail(Argument::type('string'))
                ->willReturn(new Contributor())
                ->shouldBeCalledTimes(7);
        $this->repositoryFacade
            ->flush()
                ->shouldNotBeCalled();

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }

    public function testOnlyIteratesContributorsSinceGivenDate()
    {
        $options = [
            'project_id' => 1,
            'since_datetime' => '2010-01-04T19:00:00+01:00'
        ];
        
        $this->repositoryFacade
            ->findContributorByEmail(Argument::type('string'))
                ->willReturn(new Contributor())
                ->shouldBeCalledTimes(3);
        $this->repositoryFacade
            ->flush()
            ->shouldNotBeCalled();

        $aggregator = $this->getGitLog();
        
        $aggregator->aggregate($options);
    }

    public function testCreateNewContributors()
    {
        $options = [
            'project_id' => 1,
            'update_contributors' => true,
        ];

        $this->repositoryFacade
            ->findContributorByEmail(Argument::type('string'))
            ->will(function ($args) {
                $c = ('gandalf@middle-earth' == $args[0]) ? new Contributor() : null;

                return $c;
            });

        $newContributors = [
            'Frodo Baggins' => 'frodo@shire',
            'Samwise Gamgee' => 'sam@shire',
            'Legolas' => 'legolas@mirkwood',
        ];

        foreach ($newContributors as $name => $email) {

            $this->repositoryFacade
                ->persist(Argument::type(Contributor::class))
                ->shouldBeCalled();

            $this->repositoryFacade
                ->persist(Argument::which('getEmail', $email))
                ->shouldBeCalled();

            $this->repositoryFacade
                ->persist(Argument::which('getName', $name))
                ->shouldBeCalled();
        }

        $this->repositoryFacade
            ->flush()
            ->shouldBeCalledTimes(1);

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThrowsExceptionIfNoContributorByUpdatingLog()
    {
        $options = [
            'project_id' => 1,
            'update_log' => true,
        ];

        $this->repositoryFacade
            ->findContributorByEmail(Argument::type('string'))
            ->willReturn(null);

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }

    public function testCreateNewLogEntries()
    {
        $options = [
            'project_id' => 1,
            'update_log' => true
        ];

        $this->repositoryFacade
            ->findContributorByEmail(Argument::type('string'))
            ->willReturn(new Contributor())
            ->shouldBeCalled(3);

        $this->repositoryFacade
            ->persist(Argument::type(ContributionLog::class))
            ->shouldBeCalledTimes(7);

        $this->repositoryFacade
            ->flush()
            ->shouldBeCalledTimes(1);

        $aggregator = $this->getGitLog();

        $aggregator->aggregate($options);
    }

    private function getGitLog()
    {
        return new GitLog($this->repositoryFacade->reveal(), __DIR__.'/fixtures/');
    }
}
