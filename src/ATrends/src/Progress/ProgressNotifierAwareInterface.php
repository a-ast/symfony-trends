<?php

namespace Aa\ATrends\Progress;

interface ProgressNotifierAwareInterface
{
    /**
     * @param ProgressNotifierInterface $progressNotifier
     */
    function setProgressNotifier(ProgressNotifierInterface $progressNotifier);

    /**
     * @return ProgressNotifierInterface
     */
    function getProgressNotifier();
}
