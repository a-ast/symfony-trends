<?php

namespace Aa\ATrends\Aggregator\Options;

interface OptionsInterface
{
    const SINCE_BEGINNING = 'since-beginning';

    const SINCE_LAST_UPDATE = 'since-last-update';

    public function getUpdateSince();
}
