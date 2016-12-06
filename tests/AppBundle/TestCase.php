<?php

namespace Tests\AppBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function getService($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }
}
