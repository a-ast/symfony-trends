<?php

namespace spec\AppBundle\Aggregator\Helper;

use AppBundle\Aggregator\Helper\ContributorExtractor;
use AppBundle\Aggregator\Helper\CrawlerExtractorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @mixin ContributorExtractor
 */
class ContributorExtractorSpec extends ObjectBehavior
{
    function let()
    {
        $prefixes = ['valinor://'];
        $this->beConstructedWith($prefixes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorExtractor::class);
        $this->shouldImplement(CrawlerExtractorInterface::class);
    }

    public function it_extracts_contributor_links()
    {
        $crawler = new Crawler($this->getContributorPageContent(), 'http');

        $this->extract($crawler)->shouldReturn(['valinor://fabpot', 'valinor://gandalf', 'valinor://frodo', 'valinor://legolas']);
    }


    private function getContributorPageContent()
    {
        return <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<body>

<ol class="dummy_list"></ol>

<ol class="row contributors">
    <li class="col-md-4">
        <div class="gravatar">
            <a href="valinor://fabpot">
                <img width="40" height="40" src="/" alt="Gandalf">
            </a>
        </div>

        <small>1.</small>

        <a href="valinor://gandalf">
            Gandalf
        </a>

        <small class="stats">

            <a href="https://github.com/symfony/symfony/commits/master?author=gandalf">
                <strong>11K</strong>
            </a> commits

            <strong>924K</strong> changes
        </small>
    </li>

</ol>

<div class="clear"></div>

<ol class="contributors-all" start="100">
    <li>
        <small>100.</small>
        <a href="valinor://frodo">
            Frodo Baggins
        </a>
    </li>
    <li>
        <small>101.</small>
        Samwise Gamgee
    </li>
    <li>
        <small>102.</small>
        <a href="valinor://legolas">
            Legolas
        </a>

    </li>
</ol>

</body>
</html>
EOD;
    }
}
