<?php

namespace Aa\ATrends\Api\Github;

use Aa\ATrends\Api\Github\Model\Commit;
use Aa\ATrends\Api\Github\Model\Issue;
use Aa\ATrends\Api\Github\Model\PullRequest;
use Aa\ATrends\Api\Github\Model\PullRequestComment;
use Aa\ATrends\Api\Github\Model\PullRequestReview;
use Aa\ATrends\Api\Github\Model\User;
use DateTimeInterface;
use Github\Client;
use Github\Exception\RuntimeException;
use Iterator;

class GithubApi implements GithubApiInterface
{
    const MAX_ITEMS_PER_PAGE = 100;
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
     * @return Commit[]|Iterator
     */
    public function getCommits($repositoryPath, DateTimeInterface $since = null)
    {
        $page = 1;

        while ($commits = $this->getCommitsByPage($repositoryPath, $since, $page)) {

            foreach ($commits as $commit) {
                yield Commit::createFromResponseData($commit);
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
     * @return User
     */
    public function getUser($login)
    {
        $data = $this->client->user()->show($login);

        return User::createFromResponseData($data);
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
                yield PullRequest::createFromResponseData($item);
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
        $options = ['page' => $page, 'state' => 'all', 'direction' => 'asc', 'per_page' => self::MAX_ITEMS_PER_PAGE];

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
                yield Issue::createFromResponseData($item);
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
        $options = ['page' => $page, 'state' => 'all', 'direction' => 'asc', 'per_page' => self::MAX_ITEMS_PER_PAGE];
        $options = $this->addSinceOption($options, $since);

        return $this->client->issues()->all(
            $this->getOwner($repositoryPath),
            $this->getRepo($repositoryPath),
            $options);
    }

    /**
     * @inheritdoc
     */
    public function getPullRequestReviews($repositoryPath, $pullRequestNumber)
    {
        try {
            $reviews = $this->client->pullRequests()->reviews()->configure()
                ->all(
                    $this->getOwner($repositoryPath),
                    $this->getRepo($repositoryPath),
                    $pullRequestNumber,
                    ['per_page' => self::MAX_ITEMS_PER_PAGE]
                );

            foreach ($reviews as $item) {
                yield PullRequestReview::createFromResponseData($item);
            }
        } catch (RuntimeException $e) {
            if (404 !== $e->getCode()) {
                throw $e;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getPullRequestComments($repositoryPath, DateTimeInterface $since = null)
    {
        $page = 1;

        while ($items = $this->getPullRequestCommentsByPage($repositoryPath, $since, $page)) {

            foreach ($items as $item) {
                yield PullRequestComment::createFromResponseData($item);
            }

            $page++;
        }
    }

    /**
     * @param string $repositoryPath
     * @param DateTimeInterface $since
     * @param int $page
     *
     * @return array
     */
    private function getPullRequestCommentsByPage($repositoryPath, DateTimeInterface $since = null, $page = 1)
    {
        $options = ['page' => $page, 'sort' => 'updated', 'direction' => 'asc', 'per_page' => self::MAX_ITEMS_PER_PAGE];
        $options = $this->addSinceOption($options, $since);

        return $this->client->pullRequests()->comments()->configure()
            ->all(
                $this->getOwner($repositoryPath),
                $this->getRepo($repositoryPath),
                null,
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
