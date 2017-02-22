<?php

namespace AppBundle\Aggregator\Report;

use Aa\ATrends\Aggregator\Report\ReportInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ReportDumper
{
    public function dump(ReportInterface $report, OutputInterface $output)
    {
        $table = new Table($output);

        $table
            ->addRow(['Records processed', $report->getProcessedItemCount()])
            ->render();
    }
}
