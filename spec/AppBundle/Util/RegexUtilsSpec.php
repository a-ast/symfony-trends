<?php

namespace spec\AppBundle\Util;

use AppBundle\Util\RegexUtils;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin RegexUtils
 */
class RegexUtilsSpec extends ObjectBehavior
{
    function it_returns_true_if_text_match_regex_from_list()
    {
        self::match('Frodo Baggins', ['^Bilbo', '^Frodo', '^Sam'])->shouldReturn(true);
        self::match('Frodo Baggins', ['^bilbo', '^frodo', '^sam'])->shouldReturn(true);
        self::match('Frodo Baggins', ['(udu)', '(odo)'])->shouldReturn(true);
        self::match('Frodo Baggins', ['(UDU)', '(ODO)'])->shouldReturn(true);
        self::match('Frodo said: "I wish the ring had never come to me"', ['^Frodo said: "'])->shouldReturn(true);
        self::match('Frodo # Baggins', ['^Frodo #'])->shouldReturn(true);
    }

    function it_returns_false_if_text_does_not_match_regex_from_list()
    {
        self::match('Frodo Baggins', ['(ada)', '(ede)'])->shouldReturn(false);
    }
}
