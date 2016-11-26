<?php


namespace AppBundle\Util;


use PHPUnit_Framework_TestCase;

class DateUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testGetDbIntervalFormat()
    {
        $this->assertEquals('%Y', DateUtils::getDbIntervalFormat(DateUtils::INTERVAL_YEAR));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetDbIntervalFormatFailsForUnknownFormat()
    {
        $this->assertEquals('%Y', DateUtils::getDbIntervalFormat('unknown'));
    }

    public function testGetDateTime()
    {
        $this->assertEquals(new \DateTime('1978-01-01'), DateUtils::getDateTime('1978', DateUtils::INTERVAL_YEAR));
        $this->assertEquals(new \DateTime('1978-12-01'), DateUtils::getDateTime('1978-12', DateUtils::INTERVAL_MONTH));
    }
}
