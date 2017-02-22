<?php

namespace Aa\ATrends\Aggregator\Report;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use LogicException;
use Traversable;

class CombinedReport implements ReportInterface, Countable, IteratorAggregate
{
    /**
     * @var ReportInterface[]
     */
    private $reports;

    /**
     * @inheritdoc
     */
    public function getProcessedItemCount()
    {
        $total = 0;

        foreach ($this->reports as $report) {
            $total += $report->getProcessedItemCount();
        }

        return $total;
    }

    /**
     * @inheritdoc
     */
    public function setProcessedItemCount($count)
    {
        throw new LogicException('It is impossible to set the processed item count for a combined report. Set item count of embedded reports.');
    }

    /**
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report)
    {
        $this->reports[] = $report;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->reports);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }
}
