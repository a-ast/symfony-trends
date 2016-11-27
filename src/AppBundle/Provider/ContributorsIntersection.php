<?php


namespace AppBundle\Provider;

use AppBundle\Chart\Chart;
use AppBundle\Entity\Project;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;

class ContributorsIntersection implements ProviderInterface
{
    /**
     * @var ContributionRepository
     */
    private $contributionRepository;
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * Constructor.
     * @param ContributionRepository $contributionRepository
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ContributionRepository $contributionRepository, ProjectRepository $projectRepository)
    {
        $this->contributionRepository = $contributionRepository;
        $this->projectRepository = $projectRepository;
    }

    public function getChart(array $options = [])
    {

        $projectLabels = $options['projects'];
        $projects = $this->projectRepository->getProjectsByLabels($projectLabels);

        $intersections = $this->contributionRepository->getContributorProjectIntersection();

//        $data = [];
//        foreach ($projects as $project) {
//            $data[$project->getId()] = [
//                'name' => $project->getName(),
//                'color' => $project->getColor(),
//                'y' => 0,
//            ];
//        }

//        foreach ($intersections as $intersection) {
//            $setProjects = explode(',', $intersection['project_ids']);
//
//            if(count($setProjects) > 1) {
//                $data[$intersection['project_ids']]['y'] = (int)$intersection['contributor_count'];
//            };
//
//            foreach ($setProjects as $setProject) {
//                $data[$setProject]['y'] += (int)$intersection['contributor_count'];
//            }
//        }

//        foreach ($data as $setId => &$item) {
//            $item['sets'] = explode(',', $setId);
//            if(isset($item['name'])) {
//                $item['name'] = sprintf('%s (%d)', $item['name'], $item['y']);
//            } else {
//                $item['name'] = (string)$item['y'];
//            }
//        }

        //return array_values($data);

        $series = [];
        foreach ($intersections as $item) {
            $seriesTitle = $this->getSeriesTitle($item['project_ids'], $projects);
            $seriesColor = $this->getSeriesColor($item['project_ids'], $projects);
            $series[] = [
                'y' => (int)$item['contributor_count'],
                'name' => $seriesTitle,
                'color' => $seriesColor
            ];
        }

        $chart = new Chart($options['chart']);
        $chart->addSeries($series);

        return $chart;
    }

    /**
     * @param $key
     * @param array|Project[] $projects
     *
     * @return string
     */
    private function getSeriesTitle($key, array $projects)
    {
        $projectsIds = explode(',', $key);

        if(1 === count($projectsIds)) {
            return 'Only '.$projects[$key]->getName();
        };

        return 'Contributors to both projects';
    }

    /**
     * @param $key
     * @param array|Project[] $projects
     *
     * @return string
     */
    private function getSeriesColor($key, array $projects)
    {
        $projectsIds = explode(',', $key);

        if(1 === count($projectsIds)) {
            return $projects[$key]->getColor();
        };

        return '#48845E';
    }
}
