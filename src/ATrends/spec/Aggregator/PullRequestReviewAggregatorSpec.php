<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\PullRequestReviewAggregator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PullRequestReviewAggregatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PullRequestReviewAggregator::class);
    }
}
