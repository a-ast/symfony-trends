<?php

namespace AppBundle\Formatter;

use AppBundle\Chart\Chart;

interface FormatterInterface
{
    /**
     * Format data for specific chart library
     *
     * @param Chart $chart
     *
     * @return array
     */
    public function format($chart);
}
