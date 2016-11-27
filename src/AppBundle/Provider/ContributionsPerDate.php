<?php

namespace AppBundle\Provider;

use AppBundle\Chart\Chart;
use AppBundle\Entity\Project;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Util\DateUtils;
use AppBundle\Util\StringUtils;

class ContributionsPerDate implements ProviderInterface
{
    /**
     * @var ContributionRepository
     */
    private $repository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * Constructor.
     *
     * @param ContributionRepository $repository
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ContributionRepository $repository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function getChart(array $options = [])
    {
        $projectLabels = $options['projects'];
        $projects = $this->projectRepository->getProjectsByLabels($projectLabels);
        $projectIds = array_keys($projects);

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
                $commitCount = (int)$item['contribution_count'];
                $series[$projectId][] = [$date, $commitCount];

                if (true === $includeCoreTeamCommits) {
                    $coreCommitCount = (int)$item['core_team_contribution_count'];
                    $series['core-'.$projectId][] = [$date, $coreCommitCount];
                }
            }
        }

        $chart = new Chart($options['chart']);
        foreach ($series as $key => $item) {
            $seriesTitle = $this->getSeriesTitle($key, $projects, $includeCoreTeamCommits);
            $seriesColor = $this->getSeriesColor($key, $projects, $includeCoreTeamCommits);
            $chart->addSeries($item, $seriesTitle, $seriesColor);
        }

        return $chart;
    }

    /**
     * @param string $seriesKey
     * @param array|Project[] $projects
     * @param bool $includeCoreTeamCommits
     *
     * @return string
     */
    private function getSeriesTitle($seriesKey, array $projects, $includeCoreTeamCommits)
    {
        if (isset($projects[$seriesKey])) {
            $title = $projects[$seriesKey]->getName();

            if ($includeCoreTeamCommits) {
                $title = 'All contributors';
            }

            return $title;
        }

        if ($this->isKeyOfCoreTeamSeries($seriesKey, $projects)) {
            return 'Core team';
        }

        return '';
    }

    /**
     * @param string $seriesKey
     * @param array|Project[] $projects
     * @param bool $includeCoreTeamCommits
     *
     * @return string
     */
    private function getSeriesColor($seriesKey, array $projects, $includeCoreTeamCommits)
    {
        if (isset($projects[$seriesKey])) {
            return $projects[$seriesKey]->getColor();
        }

        if ($this->isKeyOfCoreTeamSeries($seriesKey, $projects)) {
            return '#000000';
        }

        return '';
    }

    /**
     * @param string $seriesKey
     * @param array $projects
     * @return bool
     */
    private function isKeyOfCoreTeamSeries($seriesKey, array $projects)
    {
        return StringUtils::contains($seriesKey, 'core-') &&
            isset($projects[StringUtils::textAfter($seriesKey, 'core-')]);
    }
}
