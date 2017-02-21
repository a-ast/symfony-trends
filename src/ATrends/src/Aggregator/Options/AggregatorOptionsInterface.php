<?php

namespace Aa\ATrends\Aggregator\Options;

interface AggregatorOptionsInterface
{
    const SINCE_BEGINNING = 'since-beginning';

    const SINCE_LAST_UPDATE = 'since-last-update';

    public function getUpdateSince();
}
