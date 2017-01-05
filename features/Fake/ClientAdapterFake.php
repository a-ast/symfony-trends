<?php


namespace features\Fake;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Model\GithubCommit;
use DateTimeInterface;
use Iterator;

class ClientAdapterFake implements ClientAdapterInterface
{
    /**
     * @var array
     */
    private $commits;

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
        // TODO: Implement getUser() method.
    }
}
