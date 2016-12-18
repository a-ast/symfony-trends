<?php

namespace AppBundle\Command;

use AppBundle\Provider\SeriesProvider;
use AppBundle\Repository\ContributionRepository;
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

        $chartData = $this->getContainer()->getParameter('trends');

//        $chartsToGenerate = $input->getArgument('charts');
//        $this->generateJson($chartData, $chartsToGenerate, $rootDir);

        $layoutData = $this->getContainer()->getParameter('trends-layout');
        $this->generateHtml($layoutData, $chartData);
    }

    /**
     * @param string $templateId
     * @param array $data
     */
    private function dumpPage($templateId, array $data)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        /** @var TwigEngine $twig */
        $twig = $this->getContainer()->get('templating');

        $indexFile = $twig->render(sprintf('::%s.html.twig', $templateId), $data);

        $filePath = sprintf('%s/../web/trends/%s.html', $rootDir, $templateId);

        $fs = new Filesystem();
        $fs->dumpFile($filePath, $indexFile);
    }

    /**
     * @param array $chartData
     * @param array $chartsToGenerate
     * @param string $rootDir
     */
    protected function generateJson(array $chartData, array $chartsToGenerate, $rootDir)
    {
        /** @var SeriesProvider $seriesProvider */
        $seriesProvider = $this->getContainer()->get('series_provider');

        $fs = new Filesystem();

        foreach ($chartData as $chartId => $chartDefinition) {

            if (0 !== count($chartsToGenerate) && !in_array($chartId, $chartsToGenerate)) {
                continue;
            }

            $seriesOptions = $chartDefinition['series'];
            $series = $seriesProvider->getSeries($seriesOptions);

            $data = [
                'options' => ['type' => $chartDefinition['type']],
                'series' => $series,
            ];

            $json = json_encode($data, JSON_PRETTY_PRINT);

            $filePath = sprintf('%s/../web/trends/data/%s.json', $rootDir, $chartId);
            $fs->dumpFile($filePath, $json);
        }
    }

    private function generateHtml(array $layoutData, array $chartData)
    {
        /** @var ContributionRepository $contributionRepository */
        $contributionRepository = $this->getContainer()->get('repository.contribution');

        // @todo: unhardcode project id
        $lastCommitDate = $contributionRepository->getLastCommitDate(1);
        $this->dumpPage('index', [
            'blocks' => $layoutData['index'],
            'charts' => $chartData,
            'last_update_time' => $lastCommitDate
        ]);

        $maintenanceCommitPatterns = $this->getContainer()->getParameter('maintenance_commit_patterns');
        $this->dumpPage('about-data', ['maintenance_commit_patterns' => $maintenanceCommitPatterns]);


//        $trends[$groupId][$chartId] = [
//            'id' => $chartId,
//            'dataFile' => sprintf('data/%s.json', $chartId),
//            'title' => $chartView['title'],
//            'chart' => $chartView['chart'],
//        ];
    }
}
