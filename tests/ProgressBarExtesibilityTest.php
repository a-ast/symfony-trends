<?php

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class ProgressBarExtesibilityTest extends PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $progressBar = new \AppBundle\Helper\ProgressBar(new DummyOutput());
        $progressBar->start(10);
    }
}

