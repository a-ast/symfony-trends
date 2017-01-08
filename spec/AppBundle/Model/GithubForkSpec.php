<?php

namespace spec\AppBundle\Model;

use AppBundle\Model\GithubFork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GithubForkSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType(GithubFork::class);
    }
}
