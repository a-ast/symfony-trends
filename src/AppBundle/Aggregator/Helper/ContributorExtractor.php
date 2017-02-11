<?php


namespace AppBundle\Aggregator\Helper;

use Aa\ATrends\Util\StringUtils;
use Symfony\Component\DomCrawler\Crawler;

class ContributorExtractor implements CrawlerExtractorInterface
{
    /**
     * @var array
     */
    private $linkPrefixes;

    public function __construct(array $linkPrefixes)
    {
        $this->linkPrefixes = $linkPrefixes;
    }

    public function extract(Crawler $crawler)
    {
        $links = $crawler->filter('a')->links();
        $linkPrefixes = $this->linkPrefixes;

        $urls = array_map(function($item) use ($linkPrefixes) {
            $uri = $item->getUri();

            foreach ($linkPrefixes as $linkPrefix) {
                if (StringUtils::startsWith($uri, $linkPrefix)) {
                    return $uri;
                }
            }

        }, $links);

        return array_values(array_unique(array_filter($urls)));
    }
}
