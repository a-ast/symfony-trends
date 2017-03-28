<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\PullRequestReview;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequestReview
 */
class PullRequestReviewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('createFromArray', [$this->getArrayData()]);
        $this->shouldHaveType(PullRequestReview::class);
    }

    function it_can_be_created_from_response_data()
    {
        $this->beConstructedThrough('createFromResponseData', [$this->getResponseData()]);
        $this->shouldHaveType(PullRequestReview::class);
    }

    private function getArrayData()
    {
        return [
            'id' => 100,
            'state' => 'reviewed',
            'userId' => 111,
        ];
    }

    private function getResponseData()
    {
        return [
            'id' => 100,
            'state' => 'reviewed ',
            'user' => ['id' => 111],
        ];
    }

}
