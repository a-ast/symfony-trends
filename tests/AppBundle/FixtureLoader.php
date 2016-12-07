<?php

namespace Tests\AppBundle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nelmio\Alice\Fixtures;

class FixtureLoader
{
    /**
     * @var string
     */
    private $fixtureDir;

    /**
     * @var EntityManagerInterface
     */
    private $objectManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $objectManager
     * @param string $fixtureDir
     */
    public function __construct(EntityManagerInterface $objectManager, $fixtureDir)
    {
        $this->fixtureDir = $fixtureDir;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $fixtureDir
     *
     * @return $this
     */
    public function setFixtureDir($fixtureDir)
    {
        $this->fixtureDir = $fixtureDir;

        return $this;
    }

    /**
     * @param array $fileNames
     * @param bool $append
     */
    public function loadFixtureFiles(array $fileNames, $append = false)
    {
        if (false === $append) {
            $this->recreateDoctrineSchema();
        }

        $dir = $this->fixtureDir;

        array_walk($fileNames, function(&$item) use ($dir) {
           $item = $dir.'/'.$item;
        });

        Fixtures::load($fileNames, $this->objectManager);
    }

    /**
     * @param array $fixtures
     * @param bool $append
     */
    public function loadFixtures(array $fixtures, $append = false)
    {
        if (false === $append) {
            $this->recreateDoctrineSchema();
        }

        foreach ($fixtures as $entityClassName => $fixture) {
            Fixtures::load([$entityClassName => $fixture], $this->objectManager);
        }
    }

    /**
     * Load fixtures from associative array with keys: files, entities
     *
     * @param array $fixtures
     * @param bool $append
     */
    public function load(array $fixtures, $append = false)
    {
        if (isset($fixtures['files'])) {
            $this->loadFixtureFiles($fixtures['files'], $append);
        }

        if (isset($fixtures['entities'])) {
            $this->loadFixtures($fixtures['entities'], isset($fixtures['files']));
        }
    }

    /**
     * Creates schema for doctrine entities
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function recreateDoctrineSchema()
    {
        $metadata = $this->objectManager->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->objectManager);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }
}
