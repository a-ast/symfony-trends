<?php

namespace Tests\AppBundle;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    /**
     * @param array $expected
     * @param array $entities
     * @param string $keyProperty
     */
    protected function assertEqualsToFixtureData(array $expected, array $entities, $keyProperty)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($expected as $item) {

            $realItem = $entities[$item[$keyProperty]];

            foreach ($item as $propertyName => $propertyValue) {

                $realValue = $accessor->getValue($realItem, $propertyName);
                $this->assertEquals($propertyValue, $realValue);
            }
        }

        $this->assertEquals(count($expected), count($entities));
    }
}
