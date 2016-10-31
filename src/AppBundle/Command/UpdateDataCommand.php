<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:update')
            ->setDescription('Update raw data');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AggregatorInterface $aggregator */
        $aggregator = $this->getContainer()->get('aggregator.contributor_count2');
        $aggregator->aggregate(['project_id' => 1]);
    }
}
