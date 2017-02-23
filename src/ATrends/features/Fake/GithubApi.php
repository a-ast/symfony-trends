<?php


namespace features\Aa\ATrends\Fake;

use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Api\Github\Model\Commit;
use Aa\ATrends\Api\Github\Model\Issue;
use Aa\ATrends\Api\Github\Model\PullRequest;
use Aa\ATrends\Api\Github\Model\User;
use DateTimeInterface;
use Iterator;

class GithubApi implements GithubApiInterface
{
    use FakeDataAware;

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return Commit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null)
    {
        $commits = [];
        foreach ($this->fakeData['commits'] as $item) {
            $commits[] = Commit::createFromArray($item);
        }

        return new \ArrayIterator($commits);
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function getUser($login)
    {
        $data = $this->findDataItemByPropertyValue('users', 'login', $login);

        return User::createFromResponseData($data);
    }

    /**
     * @inheritdoc
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null)
    {
        $commits = [];
        foreach ($this->fakeData['issues'] as $item) {
            $commits[] = Issue::createFromArray($item);
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
            $commits[] = PullRequest::createFromArray($item);
        }

        return new \ArrayIterator($commits);
    }
}
