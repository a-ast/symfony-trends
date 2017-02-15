<?php


namespace Aa\ATrends\Progress;

use Symfony\Component\Console\Helper\ProgressBar as BaseProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decorator for Symfony ProgressBar
 */
class ProgressBar implements ProgressInterface
{
    /**
     * @var BaseProgressBar
     */
    private $progressBar;

    /**
     * Constructor.
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->progressBar = new BaseProgressBar($output);
        $this->progressBar->setFormat(' %current%/%max% [%bar%] %message%');
        $this->progressBar->setMessage('');
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message, $name = 'message')
    {
        $this->progressBar->setMessage($message, $name);
    }

    /**
     * @inheritdoc
     */
    public function start($max = null)
    {
        $this->progressBar->start($max);
    }

    /**
     * @inheritdoc
     */
    public function advance($step = 1)
    {
        $this->progressBar->advance($step);
    }

    /**
     * @inheritdoc
     */
    public function finish()
    {
        $this->progressBar->finish();
    }
}
