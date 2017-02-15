<?php

namespace AppBundle\Command;

use Aa\ATrends\Aggregator\BaseAggregatorInterface;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Progress\ProgressBar;
use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorRegistry;
use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Entity\Project;
use Aa\ATrends\Repository\ProjectRepository;
use Doctrine\ORM\EntityRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

        if ($aggregator instanceof AggregatorInterface) {

            $this->aggregate($aggregator, $aggregatorAlias, $output, null);


        } elseif ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->getProjects();
            foreach ($projects as $project) {
                $this->aggregate($aggregator, $aggregatorAlias, $output, $project);
            }
        }
    }

    private function aggregate(BaseAggregatorInterface $aggregator, $aggregatorAlias, OutputInterface $output, ProjectInterface $project)
    {
        $result = null;

        $title = $aggregatorAlias;
        if ($aggregator instanceof ProjectAwareAggregatorInterface) {
            $title = $project->getName().'/'.$aggregatorAlias;
        }

        $progressBar = $this->getProgressBar($output);

        if ($aggregator instanceof AggregatorInterface) {
            $result = $aggregator->aggregate([], $progressBar);
        } elseif ($aggregator instanceof ProjectAwareAggregatorInterface) {
            $result = $aggregator->aggregate($project, [], $progressBar);
        }

        $progressBar->finish();

        $this->dumpResult($output, $title, $result);
    }


    /**
     * @param OutputInterface $output
     * @return ProgressBar
     */
    private function getProgressBar(OutputInterface $output)
    {
        return new ProgressBar($output);
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
