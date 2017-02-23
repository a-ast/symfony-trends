<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\GithubPullRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubPullRequest
 */
class GithubPullRequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('createFromArray', [$this->getArrayData()]);
        $this->shouldHaveType(GithubPullRequest::class);
    }

    function it_can_be_created_from_response_data()
    {
        $this->beConstructedThrough('createFromResponseData', [$this->getResponseData()]);
        $this->shouldHaveType(GithubPullRequest::class);
    }

    private function getArrayData()
    {
        return [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I will take the Ring...',
            'userId' => 200,
            'body' => '...though I do not know the way.',
            'createdAt' => '2010-01-01T00:00:00Z',
            'updatedAt' => '2010-01-02T00:00:00Z',
            'closedAt' => '2010-01-03T00:00:00Z',
            'mergedAt' => '2010-01-04T00:00:00Z',
            'mergeSha' => 'xxx',
            'headSha' => 'yyy',
            'baseSha' => 'zzz',
            'baseRef' => '1.0',
        ];
    }

    private function getResponseData()
    {
        return [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I will take the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...though I do not know the way.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'merged_at' => '2010-01-04T00:00:00Z',
            'merge_commit_sha' => 'xxx',
            'head' => [
                'sha' => 'yyy',
            ],
            'base' => [
                'label' => 'Ring:1.0',
                'ref' => '1.0',
                'sha' => 'zzz',
            ],
        ];
    }
}
