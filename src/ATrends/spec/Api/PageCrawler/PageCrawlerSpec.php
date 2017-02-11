<?php

namespace spec\Aa\ATrends\Api\PageCrawler;

use Aa\ATrends\Api\PageCrawler\PageCrawler;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use Http\Client\HttpClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @mixin PageCrawler
 */
class PageCrawlerSpec extends ObjectBehavior
{
    function let(HttpClient $client, ResponseInterface $response, StreamInterface $stream)
    {
        $stream->getContents()->willReturn('');
        $response->getBody()->willReturn($stream);
        $client->sendRequest(Argument::type(RequestInterface::class))->willReturn($response);
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PageCrawler::class);
        $this->shouldImplement(PageCrawlerInterface::class);
    }

    function it_gets_dom_crawler()
    {
        $this->getDomCrawler('valinor://gandalf')->shouldHaveType(Crawler::class);
    }
}
