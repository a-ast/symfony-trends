<?php

namespace Aa\ATrends\Progress;

interface ProgressNotifierInterface
{
    /**
     * Start progress
     */
    function start();

    /**
     * Finish progress
     */
    function finish();

    /**
     * @param int $step
     */
    function advance($step = 1);

    /**
     * @param string $message
     */
    function setMessage($message);

    /**
     * @param $initiator
     */
    function setInitiator($initiator);
}
