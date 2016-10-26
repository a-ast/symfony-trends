<?php


namespace Tests\AppBundle\ValueObject;


use PHPUnit_Framework_TestCase;
use src\AppBundle\ValueObject\GitLogEntry;

class GitLogEntryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $logEntry = new GitLogEntry('');
    }
}
