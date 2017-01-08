<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;

class GithubFork implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $githubApi;
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    public function __construct(GithubApiInterface $githubApi, ContributorRepository $contributorRepository)
    {
        $this->githubApi = $githubApi;
        $this->contributorRepository = $contributorRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        foreach ($this->githubApi->getForks($project->getGithubPath()) as $fork) {

            $forkData = $fork->getData();

            $sc = $forkData["stargazers_count"];
            $wc = $forkData["watchers_count"];
            $fc = $forkData["forks_count"];
            $oic = $forkData["open_issues_count"];

            $f = $forkData["forks"];
            $oi = $forkData["open_issues"];
            $w = $forkData["watchers"];
            $p = $forkData["private"];

            if ($fc > 0) {
                print 'Forked: '.$forkData['full_name'].PHP_EOL;
            }

            if ($wc > 0) {
                print 'Watched: '.$forkData['full_name'].PHP_EOL;
            }

            //print sprintf('%s %s %s %s %s %s %s %s', $sc, $wc, $w, $fc, $f, $oic, $oi, (int)$p).PHP_EOL;

        }
    }
}
