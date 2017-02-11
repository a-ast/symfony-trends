<?php


namespace features\Aa\ATrends\Fake;

use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Model\GithubCommit;
use Aa\ATrends\Model\GithubIssue;
use Aa\ATrends\Model\GithubPullRequest;
use Aa\ATrends\Model\GithubUser;
use DateTimeInterface;
use Iterator;

class GithubApi implements GithubApiInterface
{
    use FakeDataAware;

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return GithubCommit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null)
    {
        $commits = [];
        foreach ($this->fakeData['commits'] as $item) {
            $commits[] = GithubCommit::createFromArray($item);
        }

        return new \ArrayIterator($commits);
    }

    /**
     * @param string $login
     *
     * @return GithubUser
     */
    public function getUser($login)
    {
        $data = $this->findDataItemByPropertyValue('users', 'login', $login);

        return GithubUser::createFromResponseData($data);
    }

    /**
     * @inheritdoc
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null)
    {
        $commits = [];
        foreach ($this->fakeData['issues'] as $item) {
            $commits[] = GithubIssue::createFromArray($item);
        }

        return new \ArrayIterator($commits);
    }

    /**
     * @inheritdoc
     */
    public function getPullRequests($repositoryPath)
    {
        $commits = [];
        foreach ($this->fakeData['pull-requests'] as $item) {
            $commits[] = GithubPullRequest::createFromArray($item);
        }

        return new \ArrayIterator($commits);
    }
}
