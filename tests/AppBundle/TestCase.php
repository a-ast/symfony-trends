<?php

namespace Tests\AppBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    /**
     * @var FixtureLoader
     */
    protected $fixtureLoader;

    protected function setUp()
    {
        self::bootKernel();

        $this->fixtureLoader = new FixtureLoader('');
    }

    public function getService($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }
}
