<?php
namespace AppBundle\Client\Github;

use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubUser;
use DateTimeInterface;

interface ClientAdapterInterface
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
}
