<?php

namespace spec\Aa\ATrends\Aggregator\Report;

use Aa\ATrends\Aggregator\Report\Report;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Report
 */
class ReportSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Report::class);
        $this->shouldImplement(ReportInterface::class);
    }

    public function it_sets_processed_record_count()
    {
        $this->setProcessedRecordCount(10);
    }

    public function it_gets_processed_record_count()
    {
        $this->setProcessedRecordCount(10);
        $this->getProcessedRecordCount()->shouldReturn(10);
    }
}
