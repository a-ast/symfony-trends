<?php

namespace AppBundle\Provider;

use AppBundle\Repository\ContributionRepository;

class ContributionsPerDate implements ProviderInterface
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
        $projects = $options['projects'];
        $interval = $options['interval'];

        $data = $this->repository->getContributionsPerDate($projects, $interval);

        foreach ($data as &$item) {
            $item['label'] = $item['date'];
            $item['value'] = $item['contribution_count'];
            unset($item['date']);
            unset($item['contribution_count']);
        }

        return $data;
    }
}
