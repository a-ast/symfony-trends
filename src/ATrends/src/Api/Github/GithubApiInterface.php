<?php
namespace Aa\ATrends\Api\Github;

use Aa\ATrends\Api\Github\Model\GithubCommit;
use Aa\ATrends\Api\Github\Model\GithubIssue;
use Aa\ATrends\Api\Github\Model\GithubPullRequest;
use Aa\ATrends\Api\Github\Model\GithubUser;
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
     * @param $repositoryPath
     *
     * @return GithubPullRequest[]|\Iterator
     */
    public function getPullRequests($repositoryPath);

    /**
     * @param $repositoryPath
     * @param DateTimeInterface $since
     *
     * @return GithubIssue[]|\Iterator
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null);
}
