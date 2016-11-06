<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Helper\ProgressBar;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symfony\Component\Yaml\Yaml;

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

        $progressBar = $this->getProgressBar($output);

        $result = $aggregator->aggregate($aggregatorData['options'], $progressBar);

        $output->writeln(PHP_EOL.sprintf('<info>%s: aggregation finished.</info>', $aggregatorName));

        $this->outputResults($output, $result);
    }

    /**
     * @param OutputInterface $output
     * @param $result
     */
    protected function outputResults(OutputInterface $output, $result)
    {
        $output->writeln('');

        $yaml = Yaml::dump($result, 4);

        $output->writeln($yaml);
    }

    /**
     * @param OutputInterface $output
     * @return ProgressBar
     */
    protected function getProgressBar(OutputInterface $output)
    {
        $progressBar = new ProgressBar($output);
        $progressBar->setMessage('');
        $progressBar->setMessage('');
        $progressBar->setFormat(' %current%/%max% [%bar%] %message%');

        return $progressBar;
    }
}
