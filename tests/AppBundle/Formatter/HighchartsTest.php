<?php

namespace AppBundle\Formatter;

use AppBundle\Chart\Chart;
use PHPUnit_Framework_TestCase;

class HighchartsTest extends PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $formatter = new Highcharts();

        $chart = new Chart();
        $chart
            ->setType('bar')
            ->setCategories(['a', 'b', 'c'])
            ->addSeries([1, 2, 3])
            ->addSeries([4, 5, 6])
        ;

        $expected = [
            'chart' => [
                'type' => 'bar',
            ],
            'xAxis' => [
                'categories' => ['a', 'b', 'c']
            ],
            'series' => [
                [
                    'data' => [1, 2, 3],
                ],
                [
                    'data' => [4, 5, 6],
                ],
            ]
        ];

        $formattedData = $formatter->format($chart);
        $this->assertEquals($expected, $formattedData);
    }
}
