<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\PullRequestReview;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PullRequestReviewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PullRequestReview::class);
    }
}
