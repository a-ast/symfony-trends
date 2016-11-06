<?php

namespace AppBundle\Helper;

interface ProgressInterface
{
   /**
    * Associates a text with a named placeholder.
    *
    * @param string $message The text to associate with the placeholder
    * @param string $name    The name of the placeholder
    */
    public function setMessage($message, $name = 'message');

    /**
     * Starts the progress output.
     *
     * @param int|null $max Number of steps to complete the bar (0 if indeterminate), null to leave unchanged
     */
    public function start($max = null);

    /**
     * Advances the progress output X steps.
     *
     * @param int $step Number of steps to advance
     */
    public function advance($step = 1);
}
