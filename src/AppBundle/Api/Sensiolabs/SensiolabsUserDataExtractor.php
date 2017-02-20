<?php


namespace AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use AppBundle\Model\SensiolabsUser;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsUserDataExtractor implements CrawlerExtractorInterface
{
    /**
     * @param Crawler $crawler
     *
     * @return SensiolabsUser
     */
    public function extract(Crawler $crawler)
    {
        $linksNode = $crawler->filterXPath('//section/ul[@class="tags unstyled"]');

        return SensiolabsUser::createFromArray([
            'name' => $this->getNodeText($crawler, '//h1[@itemprop="name"]'),
            'city' => $this->getNodeText($crawler, '//p[@itemprop="address"]/span[@itemprop="addressLocality"]'),
            'country' => $this->getNodeText($crawler, '//p[@itemprop="address"]/span[@itemprop="addressCountry"]'),
            'githubUrl' => $this->getLinkUrl($linksNode, 'Github'),
            'facebookUrl' => $this->getLinkUrl($linksNode, 'Facebook'),
            'twitterUrl' => $this->getLinkUrl($linksNode, 'Twitter'),
            'linkedInUrl' => $this->getLinkUrl($linksNode, 'LinkedIn'),
            'websiteUrl' => $this->getLinkUrl($linksNode, 'Website'),
            'blogUrl' => $this->getLinkUrl($linksNode, 'Blog'),
            'blogFeedUrl' => $this->getLinkUrl($linksNode, 'Blog feed')
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
