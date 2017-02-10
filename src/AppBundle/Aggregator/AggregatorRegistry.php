<?php

namespace AppBundle\Aggregator;

class AggregatorRegistry
{
    /**
     * @var BaseAggregatorInterface[]|array
     */
    private $aggregators = [];

    /**
     * @param BaseAggregatorInterface $aggregator
     * @param string $alias
     */
    public function register(BaseAggregatorInterface $aggregator, $alias)
    {
        $this->aggregators[$alias] = $aggregator;
    }

    /**
     * @param $alias
     *
     * @return BaseAggregatorInterface
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
