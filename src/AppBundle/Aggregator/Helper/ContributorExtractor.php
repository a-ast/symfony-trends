<?php


namespace AppBundle\Aggregator\Helper;

use Symfony\Component\DomCrawler\Crawler;

class ContributorExtractor
{
    public function extract($html)
    {
        $domCrawler = new Crawler($html);

        $contributors = [];

        $domCrawler
            ->filterXPath('//ol[position()>1]/li')
            ->each(function(Crawler $nodeCrawler) use (&$contributors) {

                $name = '';
                $slUrl = '';
                $nodeCrawler->filterXPath('li/text()')
                    ->each(function(Crawler $textNode) use (&$name){
                        $name .= trim($textNode->text());
                    });

                if('' === $name) {
                    $urlNode = $nodeCrawler->filterXPath('li/a');

                    $name = trim($urlNode->text());
                    $slUrl = trim($urlNode->attr('href'));

                }

                $contributors[] = [
                    'name' => $name,
                    'sensiolabs_url' => $slUrl,
                ];
            });

        return $contributors;
    }
}
