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

            $totals[$projectId] += $item['contribution_count'];
            $item['value'] = $totals[$projectId];

            unset($item['contribution_count']);
            unset($item['project_id']);
        }

        return $data;
    }

    public function getChart(array $options = [])
    {
        // TODO: Implement getChart() method.
    }
}
