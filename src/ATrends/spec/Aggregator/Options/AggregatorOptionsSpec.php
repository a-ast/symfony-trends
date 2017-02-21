<?php

namespace spec\Aa\ATrends\Aggregator\Options;

use Aa\ATrends\Aggregator\Options\AggregatorOptions;
use Aa\ATrends\Aggregator\Options\AggregatorOptionsInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin AggregatorOptions
 */
class AggregatorOptionsSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(AggregatorOptionsInterface::SINCE_BEGINNING);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AggregatorOptions::class);
    }

    public function it_returns_update_since()
    {
        $this->getUpdateSince()->shouldReturn(AggregatorOptionsInterface::SINCE_BEGINNING);
    }
}
