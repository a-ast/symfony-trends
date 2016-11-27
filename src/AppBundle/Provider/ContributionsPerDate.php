<?php

namespace AppBundle\Provider;

use AppBundle\Chart\Chart;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Util\ArrayUtils;
use AppBundle\Util\DateUtils;
use DateTime;

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

    public function getChart(array $options = [])
    {
        $projectIds = $options['projects'];
        $interval = $options['interval'];
        $includeCoreTeamCommits = (bool)$options['include_core_team_commits'];

        $series = [];

        $data = $this->repository->getContributionsPerDate($projectIds, $interval);

        foreach ($data as $item) {
            $date = DateUtils::getDateTime($item['date'], $interval);

            foreach ($projectIds as $projectId) {
                if ((int)$item['project_id'] !== $projectId) {
                    continue;
                }
                $commitCount =  (int)$item['contribution_count'];
                $series[$projectId][] = [$date, $commitCount];

                if (true === $includeCoreTeamCommits) {
                    $coreCommitCount = (int)$item['core_team_contribution_count'];
                    $series[$projectId.' core'][] = [$date, $coreCommitCount];
                }
            }
        }

        $chart = new Chart($options['chart']);
        foreach ($series as $key => $item) {
            $chart->addSeries($item, $key);
        }

        return $chart;
    }
}
