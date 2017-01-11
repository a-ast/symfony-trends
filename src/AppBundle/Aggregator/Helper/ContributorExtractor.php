<?php


namespace AppBundle\Aggregator\Helper;

use Symfony\Component\DomCrawler\Crawler;

class ContributorExtractor implements CrawlerExtractorInterface
{
    public function extract(Crawler $crawler)
    {
        $contributors = [];

        $crawler
            ->filterXPath('//ol[position()>1]/li')
            ->each(function(Crawler $nodeCrawler) use (&$contributors) {

                $name = '';
                $url = '';
                $nodeCrawler->filterXPath('li/text()')
                    ->each(function(Crawler $textNode) use (&$name){
                        $name .= trim($textNode->text());
                    });

                if ('' === $name) {
                    $urlNode = $nodeCrawler->filterXPath('li/a');

                    $name = trim($urlNode->text());
                    $url = trim($urlNode->attr('href'));
                }

                if ('' !== $url) {
                    $contributors[] = [
                        'name' => $name,
                        'url' => $url,
                    ];
                }
            });

        return $contributors;
    }
}
