<?php


namespace AppBundle\Aggregator\Helper;

use AppBundle\Aggregator\GithubApi;
use AppBundle\Util\StringUtils;
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
        $city = (0 !== $node->count()) ? $node->text() : '';

        $node = $crawler->filterXPath('//p[@itemprop="address"]/span[@itemprop="addressCountry"]');
        $country = (0 !== $node->count()) ? $node->text() : '';

        $node = $crawler->filterXPath('//section/ul[@class="tags unstyled"]');

        $githubUrl = '';
        $githubLogin = '';

        if (0 !== $node->count()) {
            $link = $node->selectLink('Github');

            if (0 !== $link->count()) {
                $githubUrl = $link->link()->getUri();
                $githubLogin = StringUtils::textAfter($githubUrl, GithubApi::PROFILE_URL);
            }
        }

        return [
            'city' => $city,
            'country' => $country,
            'github_url' => $githubUrl,
            'github_login' => $githubLogin,
        ];
    }
}
