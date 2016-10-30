<?php

namespace AppBundle\Command;

use AppBundle\Provider\ProviderInterface;
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

        //$chartId = 'commit_count_distribution_1';
        //$providerName = 'provider.commit_count_distribution';        $chartId = 'commit_count_distribution_1';

        $chartId = 'contributors_per_date_1';
        $providerName = 'provider.contributors_per_date';

        /** @var ProviderInterface $provider */
        $provider = $this->getContainer()->get($providerName);
        $data = $provider->getData();

        $fs = new Filesystem();
        $json = json_encode($data, JSON_PRETTY_PRINT);

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $filePath = sprintf('%s/../trends/data/%s.json', $rootDir, $chartId);

        $fs->dumpFile($filePath, $json);
    }
}
