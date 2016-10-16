<?php


namespace AppBundle;

use Symfony\Component\Filesystem\Filesystem;

class CrawlerOrchestrator
{
    /**
     * @var ContributorsCrawler
     */
    private $crawler;
    /**
     * @var string
     */
    private $rootDir;
    /**
     * @var DocContributorsCrawler
     */
    private $docContributorsCrawler;

    /**
     * Constructor.
     *
     * @param ContributorsCrawler $contributorsCrawler
     * @param DocContributorsCrawler $docContributorsCrawler
     * @param string $rootDir
     */
    public function __construct(ContributorsCrawler $contributorsCrawler,
        DocContributorsCrawler $docContributorsCrawler, $rootDir)
    {
        $this->crawler = $contributorsCrawler;
        $this->docContributorsCrawler = $docContributorsCrawler;
        $this->rootDir = $rootDir.'/../';
    }

    public function updateData()
    {
        $docContributors = $this->docContributorsCrawler->getData();
        $this->storeData('doc-contributors/list', $docContributors);
        
        //$this->updateContributors();
    }

    protected function updateContributors()
    {
        $versions = ['2.0', '2.1', '2.2', '2.3', '2.4', '2.5', '2.6', '2.7', '2.8', '3.0', '3.1'];

        $serie = [];

        foreach ($versions as $version) {
            $result = $this->crawler->getData($version);

            print $this->rootDir.$version."\n";
            $serie[] = [
                'text' => $version,
                'value' => $result,
            ];

            $this->storeData('contributors/'.$version, $result);
        }

        $this->storeData('contributors/serie', $serie, true);
    }

    private function storeData($key, $value, $asJson = false)
    {
        $fs = new Filesystem();
        $ext = '.data';

        if($asJson) {
            $value = json_encode($value, JSON_PRETTY_PRINT);
            $ext = '.json';
        }

        if(is_array($value)) {
            $value = implode(PHP_EOL, $value);
        }

        $fs->dumpFile($this->rootDir.'trends/raw/'.$key.$ext, $value);
    }
}
