<?php

namespace spec\Aa\ATrends\Util;

use Aa\ATrends\Util\StringUtils;
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

    public function it_returns_substring_after_giving_text()
    {
        self::textAfter('A wizard is never late, nor is he early.', 'A wizard is never late, ')->shouldReturn('nor is he early.');

        self::textAfter('A wizard is never late, nor is he early.', 'Fly, you fools!')->shouldReturn('');
    }

    public function it_returns_true_if_text_contains_substring()
    {
        self::contains('A wizard is never late', 'never')->shouldReturn(true);
    }

    public function it_returns_false_if_text_does_not_contains_substring()
    {
        self::contains('A wizard is forever late', 'never')->shouldReturn(false);
    }
}
