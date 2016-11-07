<?php

namespace AppBundle\Util;

use PHPUnit_Framework_TestCase;

class ArrayUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testTrimAndMerge()
    {
        $this->assertEquals(['a', 'b', 'c', 'd'], ArrayUtils::trimMerge('a', ['b', 'c', 'd']));
        $this->assertEquals(['b', 'c', 'd'], array_values(ArrayUtils::trimMerge('', ['b', 'c', 'd'])));
        $this->assertEquals(['a', 'c', 'd'], array_values(ArrayUtils::trimMerge('a', ['', 'c', 'd'])));

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f'], array_values(ArrayUtils::trimMerge('a', ['b', 'c'], 'd', ['e', 'f'])));
        $this->assertEquals(['a', 'c', 'd', 'e', 'f'], array_values(ArrayUtils::trimMerge('a', ['', 'c'], 'd', ['e', 'f'])));
        $this->assertEquals(['a', 'b', 'c', 'e', 'f'], array_values(ArrayUtils::trimMerge('a', ['b', 'c'], false, ['e', 'f'])));
    }
}
