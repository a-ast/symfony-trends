<?php

namespace Aa\ATrends\Progress;

trait ProgressNotifierAwareTrait
{
    /**
     * @var ProgressNotifierInterface
     */
    private $progressNotifier;

    /**
     * @param ProgressNotifierInterface $progressNotifier
     */
    public function setProgressNotifier(ProgressNotifierInterface $progressNotifier)
    {
        $this->progressNotifier = $progressNotifier;

        $this->progressNotifier->setInitiator($this);
    }
}
