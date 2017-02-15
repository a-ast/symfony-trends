<?php

namespace AppBundle\Command;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorOptionBag;
use Aa\ATrends\Aggregator\AggregatorRegistry;
use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Entity\Project;
use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Event\ProgressMessageEvent;
use Aa\ATrends\Progress\ProgressBar;
use Aa\ATrends\Repository\ProjectRepository;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Yaml;

class AggregateDataCommand extends Command implements EventSubscriberInterface
{
    /**
     * @var AggregatorRegistry
     */
    private $registry;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    private $progressBar;

    /**
     * Constructor.
     */
    public function __construct(AggregatorRegistry $registry, ProjectRepository $projectRepository)
    {
        parent::__construct();
        
        $this->registry = $registry;
        $this->projectRepository = $projectRepository;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:aggregate')
            ->setDescription('Aggregate data from external sources.')
            ->addArgument('aggregator', InputArgument::OPTIONAL, 'Aggregator alias.')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'Output list of available aggregator aliases.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false !== $input->getOption('list')) {
            $this->outputAvailableAggregators($this->registry, $output);

            return;
        }

        $aggregatorAlias = $input->getArgument('aggregator');

        if (!$this->registry->has($aggregatorAlias)) {
            throw new LogicException(sprintf('Aggregator with alias <%s> not found.', $aggregatorAlias));
        }

        $aggregator = $this->registry->get($aggregatorAlias);

        if ($aggregator instanceof ProjectAwareAggregatorInterface) {

            $projects = $this->getProjects();
            foreach ($projects as $project) {
                $aggregator->setProject($project);
                $this->aggregate($aggregator, $aggregatorAlias, $output);
            }

        } elseif ($aggregator instanceof AggregatorInterface) {

            $this->aggregate($aggregator, $aggregatorAlias, $output);
        }
    }

    /**
     * @param $aggregatorRegistry
     * @param OutputInterface $output
     */
    private function outputAvailableAggregators(AggregatorRegistry $aggregatorRegistry, OutputInterface $output)
    {
        $output->writeln('Available aggregators:');
        foreach ($aggregatorRegistry->getAliases() as $aggregatorAlias) {
            $output->writeln($aggregatorAlias);
        }
        $output->writeln('');
    }

    /**
     * @return Project[]|array
     */
    private function getProjects()
    {
        return $this->projectRepository->findAll();
    }

    private function aggregate(AggregatorInterface $aggregator, $aggregatorAlias, OutputInterface $output)
    {
        $result = null;

        $title = $aggregatorAlias;
        if ($aggregator instanceof ProjectAwareAggregatorInterface) {
            $title = $aggregator->getProject()->getName().'/'.$aggregatorAlias;
        }

        $progressBar = new ProgressBar($output);
        $this->progressBar = new \Symfony\Component\Console\Helper\ProgressBar($output);

        $result = $aggregator->aggregate(new AggregatorOptionBag(), $progressBar);

        $this->progressBar->finish();

        $this->dumpResult($output, $title, $result);
    }

    /**
     * @param OutputInterface $output
     * @param $aggregatorTitle
     * @param $result
     */
    private function dumpResult(OutputInterface $output, $aggregatorTitle, $result)
    {
        $output->writeln(PHP_EOL.sprintf('<info>%s: aggregation finished.</info>', $aggregatorTitle));

        $output->writeln('');
        $yaml = Yaml::dump($result, 4);
        $output->writeln($yaml);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            ProgressAdvanceEvent::NAME => 'onProgressAdvance',
            ProgressMessageEvent::NAME => 'onProgressMessage',
        );
    }

    public function onProgressAdvance(ProgressAdvanceEvent $event)
    {
        if (null !== $this->progressBar) {
            $this->progressBar->advance($event->getAdvanceStep());
        }
    }

    public function onProgressMessage(ProgressMessageEvent $event)
    {
        if (null !== $this->progressBar) {
            $this->progressBar->setMessage($event->getMessage());
        }
    }
}
