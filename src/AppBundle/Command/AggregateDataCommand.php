<?php

namespace AppBundle\Command;

use Aa\ATrends\Aggregator\AggregatorRegistry;
use Aa\ATrends\Aggregator\AggregatorRunner;
use Aa\ATrends\Aggregator\Options\Options;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressMessageEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use LogicException;
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
     * @var AggregatorRunner
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
     * Constructor.
     * @param AggregatorRegistry $registry
     * @param AggregatorRunner $runner
     */
    public function __construct(AggregatorRegistry $registry, AggregatorRunner $runner)
    {
        parent::__construct();
        
        $this->registry = $registry;
        $this->runner = $runner;
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

        $result = $this->runner->run($aggregator, new Options(OptionsInterface::SINCE_LAST_UPDATE));

        $this->dumpResult($output, $result);
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



//    private function aggregate(AggregatorInterface $aggregator, $aggregatorAlias, OutputInterface $output)
//    {
//        $this->currentOutput = $output;
//
//        $result = null;
//
//        $title = $aggregatorAlias;
//        if ($aggregator instanceof ProjectAwareAggregatorInterface) {
//            $title = $aggregator->getProject()->getName().'/'.$aggregatorAlias;
//        }
//
//        $this->progressBar = new ProgressBar($output);
//
//        $result = $aggregator->aggregate(new AggregatorOptionBag());
//
//        $this->progressBar->finish();
//
//        $this->dumpResult($output, $result);
//    }

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
