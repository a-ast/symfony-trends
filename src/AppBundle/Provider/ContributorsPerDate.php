<?php

namespace AppBundle\Provider;

use AppBundle\Repository\ContributionRepository;

class ContributorsPerDate implements ProviderInterface
{
    /**
     * @var ContributionRepository
     */
    private $repository;

    /**
     * Constructor
     * @param ContributionRepository $repository
     */
    public function __construct(ContributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData(array $options = [])
    {
        $data = $this->repository->getContributionsPerDate(1);

        $prevDateCount = 0;

        foreach ($data as &$item) {
            $item['value'] += $prevDateCount;
            $prevDateCount = $item['value'];
        }

        return $data;
    }
}
