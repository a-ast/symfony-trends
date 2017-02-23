<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\Issue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Issue
 */
class IssueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('createFromArray', [$this->getArrayData()]);
        $this->shouldHaveType(Issue::class);
    }

    function it_can_be_created_from_response_data()
    {
        $this->beConstructedThrough('createFromResponseData', [$this->getResponseData()]);
        $this->shouldHaveType(Issue::class);
    }

    function it_fails_for_pull_request_response_data()
    {
        $responseData = array_merge($this->getResponseData(), ['pull_request' => []]);
        $this->beConstructedThrough('createFromResponseData', [$responseData]);
        $this->shouldThrow(\RuntimeException::class)->duringInstantiation();
    }

    private function getArrayData()
    {
        return [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I need the Ring...',
            'userId' => 200,
            'body' => '...but I fear.',
            'createdAt' => '2010-01-01T00:00:00Z',
            'updatedAt' => '2010-01-02T00:00:00Z',
            'closedAt' => '2010-01-03T00:00:00Z',
            'labels' => [
                ['id' => 1, 'name' => 'Bug'],
                ['id' => 2, 'name' => 'Feature'],
            ]
        ];
    }

    private function getResponseData()
    {
        return [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I need the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...but I fear.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'labels' => [
                ['id' => 1, 'name' => 'Bug'],
                ['id' => 2, 'name' => 'Feature'],
            ]
        ];
    }
}
