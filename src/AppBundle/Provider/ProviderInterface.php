<?php

namespace AppBundle\Provider;

interface ProviderInterface
{
    public function getData(array $options = []);

    public function getChart(array $options = []);
}
