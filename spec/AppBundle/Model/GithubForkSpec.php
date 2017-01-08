<?php

namespace spec\AppBundle\Model;

use AppBundle\Model\GithubFork;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubFork
 */
class GithubForkSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(1, 100, new DateTimeImmutable(), new DateTimeImmutable(), new DateTimeImmutable());
        $this->shouldHaveType(GithubFork::class);
    }
}
