<?php

namespace AppBundle\Chart;

class Chart implements ChartInterface
{
    /**
     * @var string
     */
    private $type = '';

    /**
     * @var string
     */
    private $subType = '';

    /**
     * @var array
     */
    private $categories = [];

    /**
     * @var array
     */
    private $series = [];

    /**
     * @var array
     */
    private $xAxis = [];

    /**
     * @var array
     */
    private $seriesNames = [];

    /**
     * @var array
     */
    private $seriesColors = [];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->type = $options['type'];

        if (isset($options['sub-type'])) {
            $this->subType = $options['sub-type'];
        }

        if (isset($options['x-axis'])) {
            $this->xAxis = $options['x-axis'];
        }
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $categories
     *
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $series
     *
     * @param string $name
     * @param string $color
     *
     * @return $this
     */
    public function addSeries(array $series, $name = '', $color = '')
    {
        $this->series[] = $series;

        if ('' !== $name) {
            $this->seriesNames[] = $name;
        }

        if ('' !== $color) {
            $this->seriesColors[] = $color;
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
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @param string $subType
     *
     * @return $this
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * @return string
     */
    public function getXAxisType()
    {
        return (isset($this->xAxis['type'])) ? $this->xAxis['type'] : '';
    }

    /**
     * @return array
     */
    public function getSeriesNames()
    {
        return $this->seriesNames;
    }

    /**
     * @return array
     */
    public function getSeriesColors()
    {
        return $this->seriesColors;
    }
}
