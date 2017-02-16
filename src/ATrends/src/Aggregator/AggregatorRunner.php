<?php

namespace Aa\ATrends\Aggregator;

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

    public function run(AggregatorInterface $aggregator, AggregatorOptionsInterface $options)
    {
        if ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->repository->findAll();

            foreach ($projects as $project) {
                $aggregator->setProject($project);
                $this->runAggregator($aggregator, $options);
            }

        } else {
            $this->runAggregator($aggregator, $options);
        }
    }

    /**
     * @param AggregatorInterface $aggregator
     * @param AggregatorOptionsInterface $options
     */
    private function runAggregator(AggregatorInterface $aggregator, AggregatorOptionsInterface $options)
    {
        $this->notifyProgressStart($aggregator);

        $aggregator->aggregate($options);

        $this->notifyProgressFinish($aggregator);
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
