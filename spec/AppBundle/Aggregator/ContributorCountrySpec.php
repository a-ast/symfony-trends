<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\ContributorCountry;
use Geocoder\Geocoder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContributorCountrySpec extends ObjectBehavior
{
    function it_is_initializable(Geocoder $geocoder)
    {
        $this->beConstructedWith($geocoder);
        $this->shouldHaveType(ContributorCountry::class);
        $this->shouldImplement(AggregatorInterface::class);
    }
}
