<?php


namespace features\Fake;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubUser;
use DateTimeInterface;
use Iterator;

class ClientAdapterFake implements ClientAdapterInterface
{
    /**
     * @var GithubCommit[]|array
     */
    private $commits;

    /**
     * @var GithubUser[]|array
     */
    private $users;

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return GithubCommit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null)
    {
        return new \ArrayIterator($this->commits);
    }

    /**
     * @param GithubCommit $commit
     */
    public function addCommit(GithubCommit $commit)
    {
        $this->commits[] = $commit;
    }

    /**
     * @param string $login
     *
     * @return array
     */
    public function getUser($login)
    {
        return $this->users[$login];
    }

    /**
     * @param string $login
     * @param GithubUser $user
     */
    public function addUser($login, GithubUser $user)
    {
        $this->users[$login] = $user;
    }
}
