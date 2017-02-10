<?php

namespace AppBundle\Aggregator;

use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use Exception;
use Geocoder\Geocoder;

class ContributorCountryAggregator implements AggregatorInterface
{
    /**
     * @var Geocoder
     */
    private $geocoder;
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * Constructor.
     * @param Geocoder $geocoder
     * @param ContributorRepository $contributorRepository
     */
    public function __construct(Geocoder $geocoder, ContributorRepository $contributorRepository)
    {
        $this->geocoder = $geocoder;
        $this->contributorRepository = $contributorRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $contributors = $this->contributorRepository->findWithoutCountry();

        foreach ($contributors as $contributor) {

            try {
                $addresses = $this->geocoder->geocode($contributor->getGithubLocation());
            } catch (Exception $e) {
                continue;
            }

            $address = $addresses->first();

            $country = $address->getCountry();

            if (null !== $country->getName()) {
                $name = $country->getName();

                if (!empty($name)) {
                    $contributor->setCountry($name);
                    $this->contributorRepository->saveContributor($contributor);
                }
            }
        }
    }
}
