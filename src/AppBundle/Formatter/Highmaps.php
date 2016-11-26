<?php

namespace AppBundle\Formatter;

use AppBundle\Chart\ChartMap;

class Highmaps implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function format($chart)
    {

        $data = [
            'chart' => [
                'type' => 'map',
                'map' => 'custom/world',
            ],
            'title' => [
                'text' => null,
            ],
            'credits' => [
                'enabled' => false,
            ],
            'legend' => [
                'enabled' => false,
            ],
            'colorAxis' => [
                'min' => 1,
            ],
            'plotOptions' => [
                'map' => [
                    'joinBy' => ['iso-a2', 'iso'],
                    'nullColor' => '#fff'
                ]
            ],
        ];

        foreach ($chart->getSeries() as $series) {
            $seriesView = [];
            $seriesView['data'] = $series;
            $data['series'][] = $seriesView;
        }

        return $data;
    }
}
