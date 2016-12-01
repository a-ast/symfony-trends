<?php

namespace AppBundle\Command;

use AppBundle\Formatter\FormatterInterface;
use AppBundle\Provider\ProviderInterface;
use AppBundle\Provider\SeriesProvider;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateChartDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:generate')
            ->setDescription('Generate files for charts')
            ->addArgument('charts', InputArgument::IS_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $chartsToGenerate = $input->getArgument('charts');

        $chartData = $this->getContainer()->getParameter('trends');

        /** @var TwigEngine $twig */
        $twig = $this->getContainer()->get('templating');

        /** @var SeriesProvider $seriesProvider */
        $seriesProvider = $this->getContainer()->get('series_provider');

        $fs = new Filesystem();

        $trends = [];

        foreach ($chartData as $groupId => $groupData) {
            foreach ($groupData as $chartId => $chartView) {

                if (0 !== count($chartsToGenerate) && !in_array($chartId, $chartsToGenerate)) {
                    continue;
                }

                $seriesOptions = $chartView['series'];
                $series = $seriesProvider->getSeries($seriesOptions);

                $data = [
                    'options' => $chartView['chart'],
                    'series' => $series,
                ];

                $json = json_encode($data, JSON_PRETTY_PRINT);

                $filePath = sprintf('%s/../trends/data/%s_v2.json', $rootDir, $chartId);
                $fs->dumpFile($filePath, $json);

                $trends[$groupId][$chartId] = [
                    'id' => $chartId.'_v2',
                    'dataFile' => sprintf('data/%s_v2.json', $chartId),
                    'title' => $chartView['title'],
                    'chart' => $chartView['chart'],
                ];
            }
        }

        $indexFile = $twig->render('@App/index.html.twig', ['trends' => $trends]);

        $filePath = sprintf('%s/../trends/index_v2.html', $rootDir);
        $fs->dumpFile($filePath, $indexFile);
    }
}
