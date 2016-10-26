<?php

namespace AppBundle\Command;

use AppBundle\CrawlerOrchestrator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:update')
            ->setDescription('Update raw data');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crawler = $this->getContainer()->get('crawler.contributor_count');
        $crawler->getData();

        /** @var CrawlerOrchestrator $orchestrator */
//        $orchestrator = $this->getContainer()->get('crawler_orchestrator');
//
//        $orchestrator->updateData();
    }
}
