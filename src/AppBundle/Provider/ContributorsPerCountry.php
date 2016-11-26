<?php

namespace AppBundle\Provider;

use AppBundle\Repository\ContributorRepository;

class ContributorsPerCountry implements ProviderInterface
{
    /**
     * @var ContributorRepository
     */
    private $repository;

    /**
     * Constructor
     * @param ContributorRepository $repository
     */
    public function __construct(ContributorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData(array $options = [])
    {
        $data = $this->repository->getContributionsPerCountry();

        $combined = [];

        foreach ($data as $item) {
            if (!$item['iso_1'] && !$item['iso_2']) {
                throw new \LogicException(sprintf('ISO 3 code for country is not defined, check contributor with id %d.', $item['id']));
            }

            if ($item['iso_1']) {
                $combined[] = $item['iso_1'];
            } elseif ($item['iso_2']) {
                $combined[] = $item['iso_2'];
            }
        }

        $counts = array_count_values($combined);

        $result = [];

        foreach ($counts as $iso => $count) {
            $result[] = [$iso, $count];
        }

        return $result;
    }

    public function getChart(array $options = [])
    {
        // TODO: Implement getChart() method.
    }
}
