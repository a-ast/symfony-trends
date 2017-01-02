<?php
namespace AppBundle\Client\Github;

use DateTimeInterface;

interface ClientAdapterInterface
{
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
