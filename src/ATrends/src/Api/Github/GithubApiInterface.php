<?php
namespace Aa\ATrends\Api\Github;

use Aa\ATrends\Api\Github\Model\Commit;
use Aa\ATrends\Api\Github\Model\Issue;
use Aa\ATrends\Api\Github\Model\PullRequest;
use Aa\ATrends\Api\Github\Model\User;
use DateTimeInterface;

interface GithubApiInterface
{
    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return Commit[]|\Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null);

    /**
     * @param string $login
     *
     * @return User
     */
    public function getUser($login);

    /**
     * @param string $repositoryPath
     *
     * @return PullRequest[]|\Iterator
     */
    public function getPullRequests($repositoryPath);

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface $since
     *
     * @return Issue[]|\Iterator
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null);

    /**
     * @param string $organizationName
     *
     * @return array
     */
    public function getOrganizationMembers($organizationName);
}
