<?php
namespace AppBundle\Client\Github;

use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubFork;
use AppBundle\Model\GithubPullRequest;
use AppBundle\Model\GithubUser;
use DateTimeInterface;

interface GithubApiInterface
{
    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return GithubCommit[]|\Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null);

    /**
     * @param string $login
     *
     * @return GithubUser
     */
    public function getUser($login);


    /**
     * @param string $repositoryPath
     *
     * @return GithubFork[]|\Iterator
     */
    public function getForks($repositoryPath);

    /**
     * @param $repositoryPath
     *
     * @return GithubPullRequest[]|\Iterator
     */
    public function getPullRequests($repositoryPath);

    /**
     * @param $repositoryPath
     * @param DateTimeInterface $since
     *
     * @return array
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null);
}
