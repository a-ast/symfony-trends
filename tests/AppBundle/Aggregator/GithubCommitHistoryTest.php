<?php

namespace AppBundle\Aggregator;

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GithubCommitHistoryTest extends KernelTestCase 
{
    /**
     * @var ObjectManager
     */
    private $em;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function testAggregate()
    {
        Fixtures::load(__DIR__.'/fixtures/commit_history.yml', $this->em);
    }
}
