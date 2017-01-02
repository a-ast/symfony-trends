<?php

namespace spec\AppBundle\Model;

use AppBundle\Model\GithubCommit;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubCommit
 */
class GithubCommitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith($this->getCommitNestedData());
        $this->shouldHaveType(GithubCommit::class);
    }

    function it_can_be_created_from_array()
    {
        $this->beConstructedThrough('createFromArray', [[
                'sha' => 'hash-frodo',
                'date' => '2016-11-22T00:13:33Z',
                'message' => 'Added thoughts about my future way',
                'committer_id' => 300,
                'committer_name' => 'Frodo',
                'committer_email' => 'frodo.baggins@shire',
                'committer_login' => 'Frodo.B',
            ]]
        );

        $this->shouldHaveType(GithubCommit::class);
    }

    private function getCommitNestedData()
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
