<?php


namespace AppBundle\Util;

use PHPUnit_Framework_TestCase;

class StringUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testTextAfter()
    {
        $textAfter = StringUtils::textAfter('A wizard is never late, nor is he early.', 'A wizard is never late, ');
        $this->assertEquals('nor is he early.', $textAfter);

        $textAfter = StringUtils::textAfter('A wizard is never late, nor is he early.', 'Fly, you fools!');
        $this->assertEquals('', $textAfter);
    }
}
