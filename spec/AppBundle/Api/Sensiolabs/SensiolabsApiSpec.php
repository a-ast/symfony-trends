<?php

namespace spec\AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use AppBundle\Api\Sensiolabs\SensiolabsApi;
use AppBundle\Model\SensiolabsUser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin SensiolabsApi
 */
class SensiolabsApiSpec extends ObjectBehavior
{
    public function let(PageCrawlerInterface $pageCrawler, CrawlerExtractorInterface $crawlerExtractor)
    {
        $this->beConstructedWith($pageCrawler, $crawlerExtractor);
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
