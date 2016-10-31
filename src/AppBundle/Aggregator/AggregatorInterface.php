<?php

namespace AppBundle\Aggregator;

interface AggregatorInterface
{
    public function aggregate(array $options = []);
}
