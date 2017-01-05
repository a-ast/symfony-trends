<?php

namespace AppBundle\Provider;

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
