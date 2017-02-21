<?php

namespace spec\Aa\ATrends\Aggregator\Options;

use Aa\ATrends\Aggregator\Options\Options;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Options
 */
class OptionsSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(OptionsInterface::SINCE_BEGINNING);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Options::class);
    }

    public function it_returns_update_since()
    {
        $this->getUpdateSince()->shouldReturn(OptionsInterface::SINCE_BEGINNING);
    }
}
