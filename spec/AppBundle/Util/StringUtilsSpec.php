<?php

namespace spec\AppBundle\Util;

use AppBundle\Util\StringUtils;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin StringUtils
 */
class StringUtilsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StringUtils::class);
    }

    function it_returns_true_for_starts_with()
    {
        self::startsWith('Frodo Baggings', 'Frodo')->shouldReturn(true);
    }

    function it_returns_false_for_starts_with()
    {
        self::startsWith('Frodo Baggings', 'Sam')->shouldReturn(false);
    }
}
