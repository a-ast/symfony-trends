<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressInterface;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Repository\ContributorRepository;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Exception;
use Geocoder\Geocoder;

class ContributorCountryAggregator implements AggregatorInterface
{
    use ProgressNotifierAwareTrait;

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
    public function aggregate(AggregatorOptionsInterface $options)
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
