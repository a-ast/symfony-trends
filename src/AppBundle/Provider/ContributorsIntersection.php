<?php


namespace AppBundle\Provider;

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

    public function getData(array $options = [])
    {
        $intersections = $this->contributionRepository->getContributorProjectIntersection();

        /** @var Project[] $projects */
        $projects = $this->projectRepository->findAll();

        $data = [];
        foreach ($projects as $project) {
            $data[$project->getId()] = [
                'label' => $project->getName(),
                'color' => $project->getColor(),
                'size' => 0,
            ];
        }

        foreach ($intersections as $intersection) {
            $setProjects = explode(',', $intersection['project_ids']);

            if(count($setProjects) > 1) {
               $data[$intersection['project_ids']]['size'] = (int)$intersection['contributor_count'];
            };

            foreach ($setProjects as $setProject) {
                $data[$setProject]['size'] += (int)$intersection['contributor_count'];
            }

        }

        foreach ($data as $setId => &$item) {
            $item['sets'] = explode(',', $setId);
            if(isset($item['label'])) {
                $item['label'] = sprintf('%s (%d)', $item['label'], $item['size']);
            } else {
                $item['label'] = (string)$item['size'];
            }
        }

        return array_values($data);
    }

    public function getChart(array $options = [])
    {
        // TODO: Implement getChart() method.
    }
}
