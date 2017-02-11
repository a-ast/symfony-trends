<?php

namespace AppBundle\Client\Github;

use Aa\ATrends\Model\GithubCommit;
use Aa\ATrends\Model\GithubIssue;
use Aa\ATrends\Model\GithubPullRequest;
use Aa\ATrends\Model\GithubUser;
use DateTimeInterface;
use Github\Client;
use Iterator;

class GithubApi implements GithubApiInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     *
     * @return GithubCommit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null)
    {
        $page = 1;

        while ($commits = $this->getCommitsByPage($repositoryPath, $since, $page)) {

            foreach ($commits as $commit) {
                yield GithubCommit::createFromResponseData($commit);
            }

            $page++;
        }
    }

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface|null $since
     * @param int $page
     *
     * @return array
     */
    private function getCommitsByPage($repositoryPath, DateTimeInterface $since = null, $page = 1)
    {
        $options = ['page' => $page];
        $options = $this->addSinceOption($options, $since);

        return $this->client->repo()->commits()->all($this->getOwner($repositoryPath), $this->getRepo($repositoryPath), $options);
    }

    /**
     * @param string $login
     *
     * @return GithubUser
     */
    public function getUser($login)
    {
        $data = $this->client->user()->show($login);

        return GithubUser::createFromResponseData($data);
    }

    private function getOwner($repositoryPath)
    {
        $parts = explode('/', $repositoryPath);

        return $parts[0];
    }

    private function getRepo($repositoryPath)
    {
        $parts = explode('/', $repositoryPath);

        return $parts[1];
    }

    /**
     * @inheritdoc
     */
    public function getPullRequests($repositoryPath)
    {
        $page = 1;

        while ($items = $this->getPullRequestsByPage($repositoryPath, $page)) {

            foreach ($items as $item) {
                yield GithubPullRequest::createFromResponseData($item);
            }

            $page++;
        }
    }

    /**
     * @param $repositoryPath
     * @param integer $page
     *
     * @return array
     */
    private function getPullRequestsByPage($repositoryPath, $page = 1)
    {
        $options = ['page' => $page, 'state' => 'all', 'direction' => 'asc'];

        return $this->client->pullRequests()->all(
            $this->getOwner($repositoryPath),
            $this->getRepo($repositoryPath),
            $options);
    }

    /**
     * @inheritdoc
     */
    public function getIssues($repositoryPath, DateTimeInterface $since = null)
    {
        $page = 1;

        while ($items = $this->getIssuesByPage($repositoryPath, $since, $page)) {

            foreach ($items as $item) {

                if (isset($item['pull_request'])) {
                    continue;
                }

                yield GithubIssue::createFromResponseData($item);
            }

            $page++;
        }
    }

    /**
     * @param $repositoryPath
     * @param DateTimeInterface $since
     * @param integer $page
     *
     * @return array
     */
    private function getIssuesByPage($repositoryPath, DateTimeInterface $since = null, $page = 1)
    {
        $options = ['page' => $page, 'state' => 'all', 'direction' => 'asc'];
        $options = $this->addSinceOption($options, $since);

        return $this->client->issues()->all($this->getOwner($repositoryPath), $this->getRepo($repositoryPath),
            $options);
    }

    /**
     * @param array $options
     * @param DateTimeInterface $since
     * @return mixed
     */
    private function addSinceOption(array $options, DateTimeInterface $since = null)
    {
        if (null !== $since) {
            $options['since'] = $since->format('Y-m-d\TH:i:s\Z');
        }

        return $options;
    }
}
