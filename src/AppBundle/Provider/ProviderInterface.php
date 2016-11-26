<?php

namespace AppBundle\Provider;

interface ProviderInterface
{
    public function getChart(array $options = []);
}
