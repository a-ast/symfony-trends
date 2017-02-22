<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use Aa\ATrends\Repository\ProjectRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AggregatorRunner
{
    /**
     * @var ProjectRepository
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(ProjectRepository $repository, EventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function run(AggregatorInterface $aggregator, OptionsInterface $options)
    {
        if ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->repository->findAll();

            foreach ($projects as $project) {
                $aggregator->setProject($project);
                $report = $this->runAggregator($aggregator, $options);
            }

        } else {
            $report = $this->runAggregator($aggregator, $options);
        }

        return $report;
    }

    /**
     * @param AggregatorInterface $aggregator
     * @param OptionsInterface $options
     *
     * @return ReportInterface
     */
    private function runAggregator(AggregatorInterface $aggregator, OptionsInterface $options)
    {
        $this->notifyProgressStart($aggregator);

        $report = $aggregator->aggregate($options);

        $this->notifyProgressFinish($aggregator);

        return $report;
    }

    /**
     * @param AggregatorInterface $aggregator
     */
    private function notifyProgressStart(AggregatorInterface $aggregator)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(ProgressStartEvent::NAME, new ProgressStartEvent($aggregator));
        }
    }

    /**
     * @param AggregatorInterface $aggregator
     */
    private function notifyProgressFinish(AggregatorInterface $aggregator)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(ProgressFinishEvent::NAME, new ProgressFinishEvent($aggregator));
        }
    }
}
