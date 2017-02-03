<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressBar;
use AppBundle\Repository\ProjectRepository;
use Doctrine\ORM\EntityRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addArgument('aggregator', InputArgument::OPTIONAL, 'Aggregator name (see `aggregators`).')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'Output list of available aggregators')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aggregators = $this->getContainer()->getParameter('aggregators');
        if (false !== $input->getOption('list')) {
            $output->writeln('Available aggregators:');
            foreach ($aggregators as $aggregatorName => $aggregator) {
                $output->writeln($aggregatorName);
            }

            return;
        }

        $aggregatorName = $input->getArgument('aggregator');

        if(!isset($aggregators[$aggregatorName])) {
            throw new LogicException(sprintf('Aggregator <%s> is not found.', $aggregatorName));
        }

        $aggregatorData = $aggregators[$aggregatorName];

        /** @var AggregatorInterface $aggregator */
        $aggregator = $this->getContainer()->get($aggregatorData['service']);

        $progressBar = $this->getProgressBar($output);
        ProgressBar::getPlaceholderFormatterDefinition('current');

        $options = $aggregatorData['options'];

        $projectLabels = isset($aggregatorData['projects']) ? $aggregatorData['projects'] : [];
        $projects = $this->getProjects($projectLabels);

        foreach ($projects as $project) {
            $result = $aggregator->aggregate($project, $options, $progressBar);

            $output->writeln(PHP_EOL.sprintf('<info>%s: %s aggregation finished.</info>', $project->getName(), $aggregatorName));
            $this->outputResults($output, $result);
        }
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

    /**
     * @param array $projectLabels
     *
     * @return Project[]|array
     */
    protected function getProjects(array $projectLabels)
    {
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $this->getContainer()->get('repository.project');

        return $projectRepository->findByLabel($projectLabels);
    }
}
