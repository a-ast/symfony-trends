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
        $data = $this->repository->getContributionsPerDate();

        $totals = [];

        foreach ($data as &$item) {
            $projectId = $item['project_id'];

            $item['seriesId'] = $projectId;

            if(!isset($totals[$projectId])) {
                $totals[$projectId] = 0;
            };

            $item['value'] += $totals[$projectId];
            $totals[$projectId] = $item['value'];

            unset($item['project_id']);
        }

        return $data;
    }
}
