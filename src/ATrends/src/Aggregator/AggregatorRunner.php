<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Repository\ProjectRepository;

class AggregatorRunner
{
    /**
     * @var ProjectRepository
     */
    private $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(AggregatorInterface $aggregator)
    {
        if ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->repository->findAll();

            foreach ($projects as $project) {
                $aggregator->setProject($project);
                $this->aggregate($aggregator);
            }

        } else {
            $this->aggregate($aggregator);
        }
    }

//    private function aggregate(AggregatorInterface $aggregator)
//    {
//
//        $progressBar = new ProgressBar($output);
//
//        $result = $aggregator->aggregate(new AggregatorOptionBag(), $progressBar);
//
//        $progressBar->finish();
//
//        $this->dumpResult($output, $title, $result);
//    }
}
