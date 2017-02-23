<?php

namespace spec\Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Api\Github\Model\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin User
 */
class GithubUserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('frodo', 'frodo@shire', 'Bag End');
        $this->shouldHaveType(User::class);
    }

    function it_can_be_created_from_resonse_data()
    {
        $this->beConstructedThrough('createFromResponseData', [[
            'name' =>'frodo',
            'email' => 'frodo@shire',
            'location' => 'Bag End'
        ]]);

        $this->shouldHaveType(User::class);
    }
}
