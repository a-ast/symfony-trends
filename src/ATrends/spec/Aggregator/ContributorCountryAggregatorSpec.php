<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\ContributorCountryAggregator;
use Aa\ATrends\Entity\Contributor;
use Aa\ATrends\Progress\ProgressInterface;
use Aa\ATrends\Progress\ProgressNotifier;
use Aa\ATrends\Repository\ContributorRepository;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\Country;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ContributorCountryAggregator
 */
class ContributorCountryAggregatorSpec extends ObjectBehavior
{
    function let(
        Geocoder $geocoder,
        ContributorRepository $contributorRepository,
        Contributor $contributor,
        ProgressNotifier $progressNotifier
    ) {
        $this->beConstructedWith($geocoder, $contributorRepository);
        $this->setProgressNotifier($progressNotifier);

        $contributorRepository->findWithoutCountry()->willReturn([$contributor]);

        $address = new Address(null, null, null, null, null, null, null, null, new Country('Shire', 'SR'));

        $geocoder->geocode(Argument::type('string'))->willReturn(new \ArrayIterator([$address]));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorCountryAggregator::class);
        $this->shouldImplement(AggregatorInterface::class);
    }

    function it_returns_aggregated_data(AggregatorOptionsInterface $options)
    {
        $this->aggregate($options);
    }
}
