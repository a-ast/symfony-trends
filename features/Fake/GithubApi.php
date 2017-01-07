<?php


namespace features\Fake;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubUser;
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
            $commits[] = new GithubCommit($item);
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
        $data = $this->findBy('users', 'login', $login);

        return GithubUser::createFromGithubResponseData($data);
    }
}
