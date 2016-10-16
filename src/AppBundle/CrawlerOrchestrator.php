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
        $versions = ['2.0', '2.1', '2.2', '2.3', '2.4', '2.5', '2.6', '2.7', '2.8', '3.0', '3.1'];

        $serie = [];

        $fs = new Filesystem();

        foreach ($versions as $version) {
            $result = $this->crawler->getData($version);

            print $this->rootDir.$version . "\n";
            $serie[] = [
                'text' => $version,
                'value' => $result,
            ];

            $fs->dumpFile($this->rootDir.'trends/raw/contributors/'.$version, $result);
        }

        $fs->dumpFile($this->rootDir.'trends/raw/contributors/serie.json', json_encode($serie, JSON_PRETTY_PRINT));
    }
}
