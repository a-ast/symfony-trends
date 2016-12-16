<?php

namespace AppBundle\Util;

use PHPUnit_Framework_TestCase;

class RegexUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $this->assertTrue(RegexUtils::match('Frodo Baggins', ['^Bilbo', '^Frodo', '^Sam']));
        $this->assertTrue(RegexUtils::match('Frodo Baggins', ['^bilbo', '^frodo', '^sam']));
        $this->assertTrue(RegexUtils::match('Frodo Baggins', ['(udu)', '(odo)']));
        $this->assertTrue(RegexUtils::match('Frodo Baggins', ['(UDU)', '(ODO)']));
        $this->assertFalse(RegexUtils::match('Frodo Baggins', ['(ada)', '(ede)']));
        $this->assertTrue(RegexUtils::match('Frodo said: "I wish the ring had never come to me"', ['^Frodo said: "']));
        $this->assertTrue(RegexUtils::match('Frodo # Baggins', ['^Frodo #']));
    }
}
