<?php

namespace AppBundle\Chart;

class ChartMap implements ChartInterface
{
    /**
     * @var array
     */
    private $series = [];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {

    }

    /**
     * @param array $series
     *
     * @return $this
     */
    public function addSeries(array $series)
    {
        $this->series[] = $series;

        return $this;
    }

    /**
     * @return array
     */
    public function getSeries()
    {
        return $this->series;
    }
}
