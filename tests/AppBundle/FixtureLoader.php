<?php


namespace Tests\AppBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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

    public function getFixtureData($fileName)
    {
        $fullPath = $this->fixtureDir.'/'.$fileName;

        $contents = file_get_contents($fullPath);

        $data = Yaml::parse($contents);

        return $data;
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
    public function loadFixtureFilesToDatabase(array $fileNames, $append = false)
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
    public function loadFixturesToDatabase(array $fixtures, $append = false)
    {
        if (false === $append) {
            $this->recreateDoctrineSchema();
        }

        foreach ($fixtures as $entityClassName => $fixture) {
            Fixtures::load([$entityClassName => $fixture], $this->objectManager);
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
