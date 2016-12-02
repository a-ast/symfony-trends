<?php


namespace Tests\AppBundle\Provider;


use AppBundle\Provider\SeriesProvider;
use AppBundle\Repository\DataProvider;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

class SeriesProviderTest extends PHPUnit_Framework_TestCase
{
    public function testGetSeriesCollection()
    {
        $options = [
            [
                'data_source' => 'data_view',
                'criteria' => [],
                'dimensions' => ['name', 'age'],
            ]
        ];

        $dataProvider = $this->prophesize(DataProvider::class);
        $dataProvider
            ->getData(Argument::any(), Argument::any())
            ->willReturn([['id' => 1, 'name' => 'Gandalf', 'age' => 2019], ['id' => 1, 'name' => 'Frodo', 'age' => 33]]);

        $seriesProvider = new SeriesProvider($dataProvider->reveal());
        $collection = $seriesProvider->getSeries($options);

        $expected = [['data' => [['Gandalf', 2019], ['Frodo', 33]]]];
        $this->assertEquals($expected, $collection);
    }
}
