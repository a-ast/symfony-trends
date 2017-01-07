<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\ContributorCountry;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Repository\ContributorRepository;
use Geocoder\Collection;
use Geocoder\Geocoder;
use Geocoder\Location;
use Geocoder\Model\Address;
use Geocoder\Model\Country;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ContributorCountry
 */
class ContributorCountrySpec extends ObjectBehavior
{
    function let(Geocoder $geocoder,
        ContributorRepository $contributorRepository, Contributor $contributor)
    {
        $this->beConstructedWith($geocoder, $contributorRepository);

        $contributorRepository->findWithoutCountry()->willReturn([$contributor]);

        $address = new Address(null, null, null, null, null, null, null, null, new Country('Shire', 'SR'));

        $geocoder->geocode(Argument::type('string'))->willReturn(new \ArrayIterator([$address]));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorCountry::class);
        $this->shouldImplement(AggregatorInterface::class);
    }

    function it_returns_aggregated_data(Project $project)
    {
        $this->aggregate($project, []);
    }
}
