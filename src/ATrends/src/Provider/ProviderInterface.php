<?php

namespace Aa\ATrends\Provider;

interface ProviderInterface
{
    /**
     * Get series
     *
     * @param array $options
     *
     * @return array
     */
    public function getSeries(array $options);
}
