<?php

namespace AppBundle\Command;

use AppBundle\Provider\ProviderInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setDescription('Generate files for charts');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $chartData = $this->getContainer()->getParameter('trends');

        /** @var TwigEngine $twig */
        $twig = $this->getContainer()->get('templating');

        $fs = new Filesystem();

        $trends = [];

        foreach ($chartData as $groupId => $groupData) {
            foreach ($groupData as $chartId => $chart) {
                $providerId = $chart['service'];
                $options = $chart['options'];

                /** @var ProviderInterface $provider */
                $provider = $this->getContainer()->get($providerId);

                $data = $provider->getData($options);
                $json = json_encode($data, JSON_PRETTY_PRINT);

                $filePath = sprintf('%s/../trends/data/%s.json', $rootDir, $chartId);
                $fs->dumpFile($filePath, $json);

                $trends[$groupId][$chartId] = [
                    'dataFile' => sprintf('data/%s.json', $chartId),
                    'title' => $chart['title'],
                    'chart' => $chart['chart'],
                ];
            }

        }

        $indexFile = $twig->render('@App/index.html.twig', ['trends' => $trends]);

        $filePath = sprintf('%s/../trends/index.html', $rootDir);
        $fs->dumpFile($filePath, $indexFile);

    }


}
