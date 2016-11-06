<?php


namespace AppBundle\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar as BaseProgressBar;

class ProgressBar extends BaseProgressBar implements ProgressInterface
{
    private $stepWidth;

    public function start($max = null)
    {
        $this->stepWidth = $max ? Helper::strlen($max) : 4;
        parent::start($max);
    }

    /**
     * Gets the progress bar step width.
     *
     * @return int The progress bar step width
     */
    private function getStepWidth()
    {
        return $this->stepWidth;
    }
}
