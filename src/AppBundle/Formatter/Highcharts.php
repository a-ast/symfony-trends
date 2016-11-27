<?php

namespace AppBundle\Formatter;

use DateTime;
use AppBundle\Chart\Chart;

class Highcharts implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function format($chart)
    {
        $data = [
            'chart' => [
                'type' => $chart->getType(),
            ],
            'title' => [
                'text' => null,
            ],
            'credits' => [
                'enabled' => false,
            ],
            'legend' => [
                'enabled' => (1 < count($chart->getSeries())),
                'verticalAlign' => 'top',
            ],
            'xAxis' => [
                'tickLength' => 0,
            ],
            'yAxis' => [
                'endOnTick' => false,
                'title' => ['text' => null],
            ],
            'plotOptions' => [
                'area' => [
                    'marker' => ['enabled' => false],
                ],
            ],
        ];

        if ('stair-step' === $chart->getSubType()) {
            $data['plotOptions']['area']['step'] = 'right';
        }


        if ('datetime' === $chart->getXAxisType()) {
            $data['xAxis']['type'] = 'datetime';
        }

        foreach ($chart->getSeries() as $seriesIndex => $series) {
            $seriesView = [];

            // In order to correctly draw step area, prepend a series with a copy of the first series element (with empty title)
            if ('stair-step' === $chart->getSubType() && 'datetime' !== $chart->getXAxisType()) {
                $firstElement = $series[0];
                $firstElement[0] = '';

                array_unshift($series, $firstElement);
            }

            // @todo: move it to serializer
            if ('datetime' === $chart->getXAxisType()) {
                array_walk($series, function (&$item) {
                    $item[0] = 1000 * $item[0]->format('U');
                });

            } elseif ('' === $chart->getXAxisType()) {
                array_walk($series, function (&$item) {
                    if (0 < count($item) && $item[0] instanceof DateTime) {
                        $item[0] = $item[0]->format('Y');
                    }
                });
            }

            $seriesView['data'] = $series;



            if (isset($chart->getSeriesNames()[$seriesIndex])) {
                $seriesView['name'] = $chart->getSeriesNames()[$seriesIndex];
            }

            $data['series'][] = $seriesView;
        }

        if (0 < count($chart->getCategories())) {
            $data['xAxis']['categories'] = $chart->getCategories();
        } elseif ('datetime' !== $chart->getXAxisType()) {
            $data['xAxis']['categories'] = array_column($data['series'][0]['data'], 0);
        }


        return $data;
    }
}
