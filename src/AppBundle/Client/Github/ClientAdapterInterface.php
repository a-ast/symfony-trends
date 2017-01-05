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
     * @param string $login
     *
     * @return array
     */
    public function getUser($login);
}
