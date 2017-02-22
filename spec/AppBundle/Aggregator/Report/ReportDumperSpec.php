<?php

namespace spec\AppBundle\Aggregator\Report;

use AppBundle\Aggregator\Report\ReportDumper;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @mixin ReportDumper
 */
class ReportDumperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReportDumper::class);
    }

    public function it_dumps_report(ReportInterface $report)
    {
        $this->dump($report, new NullOutput());

        $report->getProcessedItemCount()->shouldHaveBeenCalled();
    }
}
