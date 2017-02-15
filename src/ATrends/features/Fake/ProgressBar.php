<?php


namespace features\Aa\ATrends\Fake;

use Aa\ATrends\Progress\ProgressInterface;

class ProgressBar implements ProgressInterface
{

    /**
     * @inheritdoc
     */
    public function setMessage($message, $name = 'message')
    {
    }

    /**
     * @inheritdoc
     */
    public function start($max = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function advance($step = 1)
    {
    }

    /**
     * @inheritdoc
     */
    public function finish()
    {
    }
}
