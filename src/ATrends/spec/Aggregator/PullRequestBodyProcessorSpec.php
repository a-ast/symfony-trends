<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\PullRequestBodyProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequestBodyProcessor
 */
class PullRequestBodyProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PullRequestBodyProcessor::class);
    }

    function it_gets_multiple_issue_numbers_from_body_of_old_format()
    {
        $body = "Bug fix: yes\nFixes the following tickets: #978, #654\n";

        $this->getIssueNumbers($body)->shouldReturn([978, 654]);
    }

    function it_gets_single_issue_numbers_from_body_of_old_format()
    {
        $body = "Bug fix: yes\nFixes the following tickets: #978\n";

        $this->getIssueNumbers($body)->shouldReturn([978]);
    }

    function it_gets_no_issue_numbers_from_body_of_old_format()
    {
        $body = "Bug fix: yes\nFixes the following tickets: \n";

        $this->getIssueNumbers($body)->shouldReturn([]);
    }

    function it_gets_issue_numbers_from_body_of_old_format_when_issues_are_at_the_end()
    {
        $body = "Bug fix: yes\nFixes the following tickets: #978";

        $this->getIssueNumbers($body)->shouldReturn([978]);
    }

    function it_gets_multiple_issue_numbers_from_body_of_new_format()
    {
        $body = "| Q             | A\r\n| Fixed tickets | #978, #654\r\n";
        $this->getIssueNumbers($body)->shouldReturn([978, 654]);

        $body = "| Q | A |\n| --- | --- |\n| Bug fix? | no |\n| Fixed tickets | #978,#654 |\n| Doc PR | - |\n";
        $this->getIssueNumbers($body)->shouldReturn([978, 654]);
    }

    function it_gets_single_issue_numbers_from_body_of_new_format()
    {
        $body = "| Q             | A\r\n| Fixed tickets | #978 \r\n";

        $this->getIssueNumbers($body)->shouldReturn([978]);
    }

    function it_gets_no_issue_numbers_from_body_of_new_format()
    {
        $body = "| Q             | A\r\n| Fixed tickets | n/a \r\n";

        $this->getIssueNumbers($body)->shouldReturn([]);
    }

    function it_gets_no_issue_numbers_from_body_of_new_format_with_new_lines_instead_of_ticket_number()
    {
        $body = "| Q             | A\r\n| ------------- | ---\r\n| Fixed tickets | \r\n| License       | MIT\r\n|";

        $this->getIssueNumbers($body)->shouldReturn([]);
    }
}
