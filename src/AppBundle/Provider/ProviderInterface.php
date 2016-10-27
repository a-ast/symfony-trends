<?php

namespace AppBundle\Provider;

interface ProviderInterface
{
    public function getData(array $options = []);
}
