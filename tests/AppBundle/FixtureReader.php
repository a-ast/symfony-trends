<?php


namespace Tests\AppBundle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Yaml\Yaml;

class FixtureReader
{
    /**
     * @var string
     */
    private $fixtureDir;

    /**
     * Constructor.
     *
     * @param string $fixtureDir
     */
    public function __construct($fixtureDir)
    {
        $this->fixtureDir = $fixtureDir;
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
}
