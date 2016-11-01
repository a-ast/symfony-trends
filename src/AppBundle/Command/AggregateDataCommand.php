<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AggregateDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:aggregate')
            ->setDescription('Aggregate data from external sources.')
            ->addArgument('aggregator', InputArgument::REQUIRED, 'Aggregator name (see `aggregators`).')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aggregatorName = $input->getArgument('aggregator');
        $aggregators = $this->getContainer()->getParameter('aggregators');

        if(!isset($aggregators[$aggregatorName])) {
            throw new LogicException(sprintf('Aggregator %s is not found.', $aggregatorName));
        }

        $aggregatorData = $aggregators[$aggregatorName];

        /** @var AggregatorInterface $aggregator */
        $aggregator = $this->getContainer()->get($aggregatorData['service']);

        $aggregator->aggregate($aggregators[$aggregatorName]['options']);

        $output->writeln(sprintf('<info>%s: aggregation finished.</info>', $aggregatorName));
    }
}
