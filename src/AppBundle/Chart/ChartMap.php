<?php

namespace AppBundle\Chart;

class ChartMap implements ChartInterface
{
    /**
     * @var array
     */
    private $series = [];

    /**
     * @var array
     */
    private $seriesNames = [];

    /**
     * @param array $series
     *
     * @param string $name
     * @return $this
     */
    public function addSeries(array $series, $name = '')
    {
        $this->series[] = $series;

        if ('' !== $name) {
            $this->seriesNames[] = $name;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @return array
     */
    public function getSeriesNames()
    {
        return $this->seriesNames;
    }
}
