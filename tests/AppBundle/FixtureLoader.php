<?php


namespace Tests\AppBundle;

use Doctrine\Common\Persistence\ObjectManager;
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
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Constructor.
     *
     * @param ObjectManager $objectManager
     * @param string $fixtureDir
     */
    public function __construct(ObjectManager $objectManager, $fixtureDir)
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

        $fs = new Filesystem();
        $tmpFileName = $fs->tempnam(sys_get_temp_dir(), 'fixture');
        $fs->dumpFile($tmpFileName, Yaml::dump($fixtures));

        Fixtures::load($tmpFileName, $this->objectManager);

        $fs->remove($tmpFileName);
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
