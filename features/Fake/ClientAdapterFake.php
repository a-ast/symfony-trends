<?php


namespace features\Fake;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Model\GithubCommit;
use DateTimeInterface;

class ClientAdapterFake implements ClientAdapterInterface
{
    /**
     * @var array
     */
    private $commits;

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     * @param int $page
     *
     * @return array
     */
    public function getCommitsByPage($repositoryPath, DateTimeInterface $since = null, $page = 1)
    {
        // allow to get 1 page, return empty array for the 2nd page
        if (2 === $page) {
            return [];
        }

        return $this->commits;
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
