<?php


namespace AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsUserDataExtractor implements CrawlerExtractorInterface
{
    /**
     * @param Crawler $crawler
     *
     * @return array
     */
    public function extract(Crawler $crawler)
    {
        $linksNode = $crawler->filterXPath('//section/ul[@class="tags unstyled"]');

        return array_filter([
            'name' => $this->getNodeText($crawler, '//h1[@itemprop="name"]'),
            'city' => $this->getNodeText($crawler, '//p[@itemprop="address"]/span[@itemprop="addressLocality"]'),
            'country' => $this->getNodeText($crawler, '//p[@itemprop="address"]/span[@itemprop="addressCountry"]'),
            'github' => $this->getLinkUrl($linksNode, 'Github'),
            'facebook' => $this->getLinkUrl($linksNode, 'Facebook'),
            'twitter' => $this->getLinkUrl($linksNode, 'Twitter'),
            'linkedin' => $this->getLinkUrl($linksNode, 'LinkedIn'),
            'website' => $this->getLinkUrl($linksNode, 'Website'),
            'blog' => $this->getLinkUrl($linksNode, 'Blog'),
            'blog_feed' => $this->getLinkUrl($linksNode, 'Blog feed'),
        ]);
    }

    /**
     * @param Crawler $node
     * @param $targetText
     *
     * @return string
     */
    private function getLinkUrl(Crawler $node, $targetText)
    {
        $url = '';

        if (0 !== $node->count()) {
            $link = $node->selectLink($targetText);

            if (0 !== $link->count()) {
                $url = $link->link()->getUri();
            }
        }

        return $url;
    }

    /**
     * @param Crawler $crawler
     * @param string $xpath
     *
     * @return string
     */
    private function getNodeText(Crawler $crawler, $xpath)
    {
        $node = $crawler->filterXPath($xpath);
        $text = (0 !== $node->count()) ? $node->text() : '';

        return $text;
    }
}
