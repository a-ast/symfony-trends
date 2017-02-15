<?php

namespace Aa\ATrends\Progress;

trait ProgressNotifierAwareTrait
{
    /**
     * @var ProgressNotifier
     */
    private $progressNotifier;

    /**
     * @param ProgressNotifier $progressNotifier
     */
    public function setProgressNotifier(ProgressNotifier $progressNotifier)
    {
        $this->progressNotifier = $progressNotifier;
    }
}
