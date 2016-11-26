<?php

namespace AppBundle\Chart;

class Chart
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
    private $xAxis;

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
}
