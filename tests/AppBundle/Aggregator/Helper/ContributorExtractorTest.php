<?php


namespace Tests\AppBundle\Aggregator\Helper;


use AppBundle\Aggregator\Helper\ContributorExtractor;
use PHPUnit_Framework_TestCase;

class ContributorExtractorTest extends PHPUnit_Framework_TestCase
{

    public function testExtractNameAndUrls()
    {
        $extractor = new ContributorExtractor();
        $html = file_get_contents(__DIR__.'/../fixtures/contributors.html');

        $contributors = $extractor->extract($html);

        $expected = [
            ['name' => 'Gandalf', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/gandalf'],
            ['name' => 'Frodo Baggins', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/frodo'],
            ['name' => 'Samwise Gamgee', 'sensiolabs_url' => ''],
            ['name' => 'Legolas', 'sensiolabs_url' => 'https://connect.sensiolabs.com/profile/legolas'],
        ];

        $this->assertEquals($expected, $contributors);
    }
}
