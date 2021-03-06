<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\Commit;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Commit
 */
class CommitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('createFromArray', [$this->getArrayData()]);
        $this->shouldHaveType(Commit::class);
    }

    function it_can_be_created_from_response_data()
    {
        $this->beConstructedThrough('createFromResponseData', [$this->getResponseData()]);
        $this->shouldHaveType(Commit::class);
    }

    private function getArrayData()
    {
        return [
            'sha' => 'hash-frodo',
            'date' => '2016-11-22T00:13:33Z',
            'message' => 'Added thoughts about my future way',
            'committer_id' => 300,
            'committer_name' => 'Frodo',
            'committer_email' => 'frodo.baggins@shire',
            'committer_login' => 'Frodo.B',
        ];
    }

    private function getResponseData()
    {
        return [
            'sha' => 'hash-frodo',
            'commit' => [
                'author' => [ 'name' => 'Frodo ext', 'email' => 'frodo.baggins.ext@shire', 'date' => '2016-11-22T00:13:33Z',],
                'message' => 'Added thoughts about my future way',
            ],
            'author' => [ 'id' => 300, 'login' => 'frodo.ext'],
        ];
    }
}
