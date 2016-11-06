<?php


namespace AppBundle\Aggregator\Helper;

use Symfony\Component\DomCrawler\Crawler;

class SensiolabsDataExtractor
{
    /**
     * @param string $html
     * @param string $url
     *
     * @return array
     */
    public function extract($html, $url)
    {
        $crawler = new Crawler($html, $url);

        $node = $crawler->filterXPath('//p[@itemprop="address"]/span[@itemprop="addressLocality"]');
        $city = $node->text();

        $node = $crawler->filterXPath('//p[@itemprop="address"]/span[@itemprop="addressCountry"]');
        $country = $node->text();

        $node = $crawler->filterXPath('//section/ul[@class="tags unstyled"]');

        $link = $crawler->selectLink('Github')->link();
        $githubUrl = $link->getUri();

        return [
            'city' => $city,
            'country' => $country,
            'github' => $githubUrl,
        ];
    }
}
