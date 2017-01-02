<?php

namespace AppBundle\Client\Github;

use Github\Client;
use Github\HttpClient\Builder;
use Http\Client\Common\Plugin;


class ClientConfigurator
{
    /**
     * @var Plugin[]|array
     */
    private $plugins;
    /**
     * @var Builder
     */
    private $builder;

    /**
     * Constructor.
     * @param Builder $builder
     * @param array $plugins
     */
    public function __construct(Builder $builder, array $plugins = [])
    {
        $this->plugins = $plugins;
        $this->builder = $builder;
    }


    /**
     * @param Client $client
     */
    public function configure(Client $client)
    {
        foreach ($this->plugins as $plugin) {
            $this->builder->addPlugin($plugin);
        }
    }
}
