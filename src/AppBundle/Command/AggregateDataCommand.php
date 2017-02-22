<?php

namespace AppBundle\Command;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\Report\ReportAwareInterface;
use LogicException;
use AppBundle\Aggregator\Report\ReportDumper;
use Aa\ATrends\Aggregator\Runner\Runner;
use Aa\ATrends\Aggregator\AggregatorRegistry;
use Aa\ATrends\Aggregator\Options\Options;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressMessageEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
     * @var Runner
     */
    private $runner;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var OutputInterface
     */
    private $currentOutput;

    /**
     * @var ReportDumper
     */
    private $reportDumper;

    /**
     * Constructor.
     * @param AggregatorRegistry $registry
     * @param Runner $runner
     * @param ReportDumper $reportDumper
     */
    public function __construct(AggregatorRegistry $registry, Runner $runner, ReportDumper $reportDumper)
    {
        parent::__construct();
        
        $this->registry = $registry;
        $this->runner = $runner;
        $this->reportDumper = $reportDumper;
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
        $this->currentOutput = $output;

        $this->runner->run($aggregator, new Options(OptionsInterface::SINCE_LAST_UPDATE));
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
     * @param OutputInterface $output
     * @param $result
     */
    private function dumpResult(OutputInterface $output, $result)
    {
        $output->writeln(PHP_EOL.sprintf('<info>Aggregation finished.</info>'));

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
            ProgressStartEvent::NAME => 'onProgressStart',
            ProgressFinishEvent::NAME => 'onProgressFinish',
            ProgressAdvanceEvent::NAME => 'onProgressAdvance',
            ProgressMessageEvent::NAME => 'onProgressMessage',
        );
    }

    public function onProgressStart(ProgressStartEvent $event)
    {
        if (null === $this->currentOutput) {
            return;
        }

        $this->progressBar = new ProgressBar($this->currentOutput);
        $this->progressBar->start();
    }

    public function onProgressFinish(ProgressFinishEvent $event)
    {
        if (null === $this->currentOutput || null === $this->progressBar) {
            return;
        }

        $this->progressBar->finish();
        $this->currentOutput->writeln('');

        if ($event->getInitiator() instanceof ReportAwareInterface) {
            /** @var ReportAwareInterface $aggregator */
            $aggregator = $event->getInitiator();

            $this->reportDumper->dump($aggregator->getReport(), $this->currentOutput);
        }
    }

    public function onProgressAdvance(ProgressAdvanceEvent $event)
    {
        if (null === $this->currentOutput || null === $this->progressBar) {
            return;
        }

        $this->progressBar->advance($event->getAdvanceStep());
    }

    public function onProgressMessage(ProgressMessageEvent $event)
    {
        if (null === $this->currentOutput || null === $this->progressBar) {
            return;
        }

        $this->progressBar->setMessage($event->getMessage());
    }
}
