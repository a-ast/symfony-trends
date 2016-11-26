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
            ],
            'xAxis' => [],
            'yAxis' => [
                'endOnTick' => false,
                'title' => ['text' => null],
            ],
        ];


        if ('datetime' === $chart->getXAxisType()) {
            $data['xAxis']['type'] = 'datetime';
        }

        foreach ($chart->getSeries() as $seriesIndex => $series) {
            $seriesView = [];

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

            if ('stair-step' === $chart->getSubType()) {
                $seriesView['step'] = 'left';
            }

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
