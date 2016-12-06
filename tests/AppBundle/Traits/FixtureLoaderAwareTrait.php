<?php

namespace AppBundle\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Tests\AppBundle\FixtureLoader;

trait FixtureLoaderAwareTrait
{
    /**
     * @var FixtureLoader
     */
    private $fixtureLoader;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $fixtureDir
     *
     * @return FixtureLoader
     */
    public function initFixtureLoader(EntityManagerInterface $entityManager, $fixtureDir)
    {
        $this->fixtureLoader = new FixtureLoader($entityManager, $fixtureDir);

        return $this->fixtureLoader;
    }

    /**
     * @return FixtureLoader
     */
    public function getFixtureLoader()
    {
        return $this->fixtureLoader;
    }
}
