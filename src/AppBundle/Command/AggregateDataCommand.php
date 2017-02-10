<?php

namespace AppBundle\Command;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\AggregatorRegistry;
use AppBundle\Aggregator\ProjectAwareAggregatorInterface;
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
            ->addArgument('aggregator', InputArgument::OPTIONAL, 'Aggregator alias.')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'Output list of available aggregator aliases.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AggregatorRegistry $aggregatorRegistry */
        $aggregatorRegistry = $this->getContainer()->get('aggregator_registry');
        if (false !== $input->getOption('list')) {
            $this->outputAvailableAggregators($aggregatorRegistry, $output);

            return;
        }

        $aggregatorAlias = $input->getArgument('aggregator');

        if(!$aggregatorRegistry->has($aggregatorAlias)) {
            throw new LogicException(sprintf('Aggregator with alias <%s> not found.', $aggregatorAlias));
        }

        $aggregator = $aggregatorRegistry->get($aggregatorAlias);

        $progressBar = $this->getProgressBar($output);

        if ($aggregator instanceof AggregatorInterface) {
            $result = $aggregator->aggregate([], $progressBar);
            $this->dumpResult($output, $aggregatorAlias, $result);

        } elseif ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->getProjects();
            foreach ($projects as $project) {
                $result = $aggregator->aggregate($project, [], $progressBar);

                $this->dumpResult($output, $project->getName().'/'.$aggregatorAlias, $result);
            }
        }
    }


    /**
     * @param OutputInterface $output
     * @return ProgressBar
     */
    private function getProgressBar(OutputInterface $output)
    {
        $progressBar = new ProgressBar($output);
        $progressBar->setMessage('');
        $progressBar->setMessage('');
        $progressBar->setFormat(' %current%/%max% [%bar%] %message%');

        ProgressBar::getPlaceholderFormatterDefinition('current');

        return $progressBar;
    }

    /**
     * @return Project[]|array
     */
    private function getProjects()
    {
        /** @var ProjectRepository $projectRepository */
        $projectRepository = $this->getContainer()->get('repository.project');

        return $projectRepository->findAll();
    }

    /**
     * @param $aggregatorRegistry
     * @param OutputInterface $output
     */
    protected function outputAvailableAggregators($aggregatorRegistry, OutputInterface $output)
    {
        $output->writeln('Available aggregators:');
        foreach ($aggregatorRegistry->getAliases() as $aggregatorAlias) {
            $output->writeln($aggregatorAlias);
        }
        $output->writeln('');
    }

    /**
     * @param OutputInterface $output
     * @param $aggregatorTitle
     * @param $result
     */
    protected function dumpResult(OutputInterface $output, $aggregatorTitle, $result)
    {
        $output->writeln(PHP_EOL.sprintf('<info>%s: aggregation finished.</info>', $aggregatorTitle));

        $output->writeln('');
        $yaml = Yaml::dump($result, 4);
        $output->writeln($yaml);
    }
}
