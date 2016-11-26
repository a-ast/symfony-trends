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

    public function getChart(array $options = [])
    {
        $projects = $options['projects'];
        $interval = $options['interval'];

        $series1 = [];
        $series2 = [];

        $data = $this->repository->getContributionsPerDate($projects, $interval);

        foreach ($data as $item) {
            $date = DateUtils::getDateTime($item['date'], $interval);
            $series1[] = [$date, (int)$item['contribution_count']];
            $series2[] = [$date, (int)$item['core_team_contribution_count']];
        }

        $chart = new Chart($options['chart']);
        $chart
            ->addSeries($series1)
            ->addSeries($series2)
        ;

        return $chart;
    }
}
