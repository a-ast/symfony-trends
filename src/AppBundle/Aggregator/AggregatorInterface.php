<?php

namespace AppBundle\Aggregator;

interface AggregatorInterface
{
    /**
     * @param array $options
     *
     * @return array
     */
    public function aggregate(array $options = []);
}
