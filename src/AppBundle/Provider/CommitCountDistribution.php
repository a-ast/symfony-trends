<?php


namespace AppBundle\Provider;

use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;

class CommitCountDistribution implements ProviderInterface
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
        $projectId = $options['project_id'];

        $commitCounts = $this->contributionRepository->getContributorCommitCounts($projectId);

        $intervals = [
            [1],
            [2],
            [3, 5],
            [5, 10],
            [10, 30],
            [30, 200],
            [200, null],
        ];

        $data = [];

        foreach ($intervals as $intervalIndex => $interval) {
            $data[$intervalIndex] = [
                'text' => $this->getIntervalLabel($interval),
                'value' => 0,
            ];
        }

        foreach ($commitCounts as $item) {
            $count = (int)$item['contribution_count'];
            foreach ($intervals as $intervalIndex => &$interval) {

                switch(count($interval)) {
                    case 1:
                        if($count === $interval[0]) {
                            $data[$intervalIndex]['value']++;
                        }

                        break;

                    default:
                        $gt = $count >= $interval[0];
                        $lt = $interval[1] === null ? true : $count < $interval[1];

                        if ($gt && $lt) {
                            $data[$intervalIndex]['value']++;
                        }
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * @param $interval
     * @return string
     */
    protected function getIntervalLabel($interval)
    {
        if(2 === count($interval) && null === $interval[1]) {
            return $interval[0].'+';
        }

        return implode('-', $interval);
    }
}
