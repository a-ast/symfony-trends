<?php
namespace AppBundle\Client\Github;

use AppBundle\Model\GithubCommit;
use DateTimeInterface;
use Iterator;

interface ClientAdapterInterface
{
    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return GithubCommit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null);

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     * @param int $page
     *
     * @return array
     */
    public function getCommitsByPage($repositoryPath, DateTimeInterface $since = null, $page = 1);

    /**
     * @param string $login
     *
     * @return array
     */
    public function getUser($login);
}
