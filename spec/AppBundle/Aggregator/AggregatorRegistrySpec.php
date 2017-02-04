<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\AggregatorRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin AggregatorRegistry
 */
class AggregatorRegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AggregatorRegistry::class);
    }

    function it_registers_aggregator(AggregatorInterface $aggregator)
    {
        $this->register($aggregator, 'alias');
    }

    function it_gets_registered_aggregator(AggregatorInterface $aggregator)
    {
        $this->register($aggregator, 'alias');

        $this->get('alias')->shouldReturn($aggregator);
    }

    function it_returns_all_aliases(AggregatorInterface $aggregator1, AggregatorInterface $aggregator2)
    {
        $this->register($aggregator1, 'alias1');
        $this->register($aggregator2, 'alias2');

        $this->getAliases()->shouldReturn(['alias1', 'alias2']);
    }

    function it_returns_true_if_aggregator_registered(AggregatorInterface $aggregator)
    {
        $this->register($aggregator, 'alias');

        $this->has('alias')->shouldReturn(true);
    }

    function it_returns_false_if_aggregator_is_not_registered()
    {
        $this->has('alias')->shouldReturn(false);
    }
}
