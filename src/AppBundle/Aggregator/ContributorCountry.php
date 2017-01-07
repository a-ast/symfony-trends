<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use Exception;
use Geocoder\Geocoder;
use Geocoder\Model\Address;

class ContributorCountry implements AggregatorInterface
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
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $contributors = $this->contributorRepository->findWithoutCountry();

        foreach ($contributors as $contributor) {

            try {
                $addresses = $this->geocoder->geocode($contributor->getGithubLocation());
            } catch (Exception $e) {
                continue;
            }

            /** @var Address $address */
            $address = $addresses[0];

            $country = $address->getCountry();
            $name = $country->getName();

            $contributor->setCountry($name);
            $this->contributorRepository->saveContributor($contributor);
        }
    }
}
