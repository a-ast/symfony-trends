<?php

namespace spec\AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use AppBundle\Api\Sensiolabs\SensiolabsApi;
use AppBundle\Model\SensiolabsUser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @mixin SensiolabsApi
 */
class SensiolabsApiSpec extends ObjectBehavior
{
    public function let(
        PageCrawlerInterface $pageCrawler,
        CrawlerExtractorInterface $extractor,
        Crawler $crawler,
        SensiolabsUser $user
    ) {
        $pageCrawler->getDomCrawler(Argument::type('string'))->willReturn($crawler);
        $extractor->extract($crawler)->willReturn($user);
        $this->beConstructedWith($pageCrawler, $extractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SensiolabsApi::class);
    }

    public function it_returns_user_profile_data()
    {
        $this->getUser('frodo')->shouldHaveType(SensiolabsUser::class);
    }
}
