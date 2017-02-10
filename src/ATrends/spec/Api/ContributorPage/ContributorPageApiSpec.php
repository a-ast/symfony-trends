<?php

namespace spec\Aa\ATrends\Api\ContributorPage;

use AppBundle\Aggregator\Helper\ContributorExtractor;
use Aa\Atrends\Api\ContributorPage\ContributorPageApi;
use Aa\Atrends\Api\ContributorPage\ContributorPageApiInterface;
use AppBundle\Client\PageCrawler\PageCrawlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @mixin ContributorPageApi
 */
class ContributorPageApiSpec extends ObjectBehavior
{
    function let(ContributorExtractor $contributorExtractor, PageCrawlerInterface $pageCrawler, Crawler $domCrawler)
    {
        $this->beConstructedWith($contributorExtractor, $pageCrawler);

        $pageCrawler->getDomCrawler('uri')->willReturn($domCrawler);
        $contributorExtractor->extract($domCrawler)->willReturn(['valinor://frodo']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorPageApi::class);
        $this->shouldImplement(ContributorPageApiInterface::class);
    }

    function it_gets_contributor_logins()
    {
        $logins = $this->getContributorLogins('uri', 'valinor://');

        $logins->shouldReturn(['frodo']);
    }
}
