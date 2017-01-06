<?php

namespace spec\AppBundle\Util;

use AppBundle\Util\ArrayUtils;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ArrayUtils
 */
class ArrayUtilsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayUtils::class);
    }

    public function it_trims_and_merges_array()
    {
        self::trimMerge(ArrayUtils::trimMerge('a', ['b', 'c', 'd']))->shouldReturn(['a', 'b', 'c', 'd']);
        self::trimMerge(ArrayUtils::trimMerge('', ['b', 'c', 'd']))->shouldReturn(['b', 'c', 'd']);
        self::trimMerge(ArrayUtils::trimMerge('a', ['', 'c', 'd']))->shouldReturn(['a', 'c', 'd']);
        self::trimMerge(ArrayUtils::trimMerge('a', ['b', 'c'], 'd', ['e', 'f']))->shouldReturn(['a', 'b', 'c', 'd', 'e', 'f']);
        self::trimMerge(ArrayUtils::trimMerge('a', ['', 'c'], 'd', ['e', 'f']))->shouldReturn(['a', 'c', 'd', 'e', 'f']);
        self::trimMerge(ArrayUtils::trimMerge('a', ['b', 'c'], false, ['e', 'f']))->shouldReturn(['a', 'b', 'c', 'e', 'f']);
    }

    public function it_trims_array()
    {
        self::trim([0 => ''])->shouldReturn([]);
    }

    public function testGetFirstNonEmptyElement()
    {
        self::getFirstNonEmptyElement(['a', 'b'])->shouldReturn('a');
        self::getFirstNonEmptyElement(['', 'b'])->shouldReturn('b');
        self::getFirstNonEmptyElement(['', ''], 'default')->shouldReturn('default');
    }
}
