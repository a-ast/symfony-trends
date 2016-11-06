<?php


namespace Tests\AppBundle\Aggregator\Helper;

use PHPUnit_Framework_TestCase;
use AppBundle\Aggregator\Helper\SensiolabsDataExtractor;

class SensiolabsDataExtractorTest extends PHPUnit_Framework_TestCase
{

    public function testExtract()
    {
        $extractor = new SensiolabsDataExtractor();
        $html = file_get_contents(__DIR__.'/../fixtures/sensiolabs-profile.html');

        $data = $extractor->extract($html, 'http://middle-earth');

        $expected = [
            'city' => 'Valinor',
            'country' => 'Middle Earth',
            'github' => 'http://valinor-github/gandalf',
        ];

        $this->assertEquals($expected, $data);
    }
}
