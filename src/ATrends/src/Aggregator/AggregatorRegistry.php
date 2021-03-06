<?php

namespace Aa\ATrends\Aggregator;

class AggregatorRegistry
{
    /**
     * @var AggregatorInterface[]|array
     */
    private $aggregators = [];

    /**
     * @param AggregatorInterface $aggregator
     * @param string $alias
     */
    public function register(AggregatorInterface $aggregator, $alias)
    {
        $this->aggregators[$alias] = $aggregator;
    }

    /**
     * @param $alias
     *
     * @return AggregatorInterface
     */
    public function get($alias)
    {
        return $this->aggregators[$alias];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return array_keys($this->aggregators);
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return isset($this->aggregators[$alias]);
    }
}
