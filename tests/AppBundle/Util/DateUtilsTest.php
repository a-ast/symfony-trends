<?php


namespace AppBundle\Util;


use PHPUnit_Framework_TestCase;

class DateUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testGetIntervalFormat()
    {
        $this->assertEquals('%Y', DateUtils::getIntervalFormat(DateUtils::INTERVAL_YEARLY));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetIntervalFormatFailsForUnknownFormat()
    {
        $this->assertEquals('%Y', DateUtils::getIntervalFormat('unknown'));
    }
}
