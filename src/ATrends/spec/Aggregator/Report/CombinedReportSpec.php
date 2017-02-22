<?php

namespace spec\Aa\ATrends\Aggregator\Report;

use Aa\ATrends\Aggregator\Report\CombinedReport;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use Countable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Traversable;

/**
 * @mixin CombinedReport
 */
class CombinedReportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CombinedReport::class);
        $this->shouldImplement(ReportInterface::class);
        $this->shouldImplement(Countable::class);
        $this->shouldImplement(Traversable::class);
    }

    public function it_adds_reports(ReportInterface $report1, ReportInterface $report2)
    {
        $this->addReport($report1);
        $this->addReport($report2);

        $this->shouldHaveCount(2);
    }

    public function it_iterates_added_reports(ReportInterface $report1, ReportInterface $report2)
    {
        $this->addReport($report1);
        $this->addReport($report2);

        foreach ($this->getWrappedObject() as $report) {
            expect($report instanceof ReportInterface);
        }
    }

    public function it_gets_total_processed_item_count_from_added_reports(ReportInterface $report1, ReportInterface $report2)
    {
        $this->addReport($report1);
        $report1->getProcessedItemCount()->willReturn(2);

        $this->addReport($report2);
        $report2->getProcessedItemCount()->willReturn(3);

        $this->getProcessedItemCount()->shouldReturn(5);
    }

    public function it_throws_exception_by_setting_processed_item_count()
    {
        $this->shouldThrow(\LogicException::class)->during('setProcessedItemCount', [0]);
    }
}
