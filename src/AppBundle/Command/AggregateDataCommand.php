<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setDescription('Aggregate data from external sources.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AggregatorInterface $aggregator */
        $aggregator = $this->getContainer()->get('aggregator.contributor_count2');
        //$aggregator = $this->getContainer()->get('aggregator.git_log');
        $aggregator->aggregate(['project_id' => 27]);
    }
}
