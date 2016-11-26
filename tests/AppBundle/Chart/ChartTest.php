<?php

namespace AppBundle\Chart;

use PHPUnit_Framework_TestCase;

class ChartTest extends PHPUnit_Framework_TestCase
{
    public function testCreateChart()
    {
        $chart = new Chart();
        $chart
            ->setType('bar')
            ->setCategories(['a', 'b', 'c'])
            ->addSeries([1, 2, 3])
            ->addSeries([4, 5, 6])
        ;

        $this->assertEquals('bar', $chart->getType());
        $this->assertEquals(['a', 'b', 'c'], $chart->getCategories());
        $this->assertEquals([[1, 2, 3], [4, 5, 6]], $chart->getSeries());
    }
}
