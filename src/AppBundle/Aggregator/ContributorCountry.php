<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use Geocoder\Geocoder;
use Geocoder\Model\Address;

class ContributorCountry implements AggregatorInterface
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * Constructor.
     * @param Geocoder $geocoder
     */
    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {

        // TODO: iterate contributors with location and without country
        // think how to exclude trash locations
        // country_id??? with WrongCountry for some countries.

        // catch Geocoder\Exception\NoResult
        // take first country
        // think how to avoid: Earth, 'somewhere' etc

        $geo = $this->geocoder->geocode('The Netherlands');

        /** @var Address $address */
        foreach ($geo as $address) {


            var_dump($address);

            // todo: set country_code????
            $address->getCountry()->getName();

        }
    }
}
