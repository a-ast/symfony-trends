<?php


namespace AppBundle\Aggregator\Helper;

use AppBundle\Aggregator\GithubApi;
use AppBundle\Util\StringUtils;
use Prophecy\Util\StringUtil;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsDataExtractor
{
    /**
     * @param Crawler $crawler
     *
     * @return array
     */
    public function extract(Crawler $crawler)
    {
        $node = $crawler->filterXPath('//p[@itemprop="address"]/span[@itemprop="addressLocality"]');
        $city = $node->text();

        $node = $crawler->filterXPath('//p[@itemprop="address"]/span[@itemprop="addressCountry"]');
        $country = $node->text();

        $node = $crawler->filterXPath('//section/ul[@class="tags unstyled"]');

        $link = $node->selectLink('Github')->link();
        $githubUrl = $link->getUri();

        return [
            'city' => $city,
            'country' => $country,
            'github_url' => $githubUrl,
            'github_login' => StringUtils::textAfter($githubUrl, GithubApi::PROFILE_URL),
        ];
    }
}
