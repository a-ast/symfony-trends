<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

class PullRequest implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $githubApi;

    public function __construct(GithubApiInterface $githubApi)
    {
        $this->githubApi = $githubApi;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $i = 0;
        $p = 0;

//        foreach ($this->githubApi->getIssues($project->getGithubPath()) as $issue) {
//
//            if (isset($issue['pull_request'])) {
//                $p++;
//            } else {
//                $i++;
//            }
//
//            print $issue['title'].PHP_EOL;
//        }

        foreach ($this->githubApi->getPullRequests($project->getGithubPath()) as $pr) {

           $p++;

            print $pr['title'].PHP_EOL;
        }


//
//        print 'Issue count:';
//        var_dump($i);

        print 'PR count:';
        var_dump($p);
    }
}
