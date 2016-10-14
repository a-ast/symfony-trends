<?php


namespace AppBundle;

use Symfony\Component\Filesystem\Filesystem;

class CrawlerOrchestrator
{
    /**
     * @var ContributorsDataCrawler
     */
    private $crawler;
    /**
     * @var string
     */
    private $rootDir;

    /**
     * Constructor.
     * 
     * @param ContributorsDataCrawler $crawler
     * @param string $rootDir
     */
    public function __construct(ContributorsDataCrawler $crawler, $rootDir)
    {
        $this->crawler = $crawler;
        $this->rootDir = $rootDir.'/../';
    }

    public function updateData()
    {
        $versions = ['2.0', '2.1', '2.2'];
        
        $fs = new Filesystem();
        
        foreach ($versions as $version) {
            $result = $this->crawler->getData($version);

            print $this->rootDir.$version . "\n";

            $fs->dumpFile($this->rootDir.'trends/raw/contributors/'.$version, $result);
        }
    }
}
