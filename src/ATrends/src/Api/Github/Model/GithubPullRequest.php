<?php

namespace Aa\ATrends\Api\Github\Model;

use DateTimeImmutable;
use DateTimeInterface;

class GithubPullRequest
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $body;

    /**
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface
     */
    private $closedAt;

    /**
     * @var DateTimeInterface
     */
    private $mergedAt;

    /**
     * @var string
     */
    private $mergeSha;

    /**
     * @var string
     */
    private $headSha;

    /**
     * @var string
     */
    private $baseSha;

    /**
     * @var string
     */
    private $baseRef;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return GithubPullRequest
     */
    public static function createFromArray(array $data)
    {
        $pullRequest = new GithubPullRequest();

        $pullRequest->id = (int) $data['id'];
        $pullRequest->number = (int) $data['number'];
        $pullRequest->state = $data['state'];
        $pullRequest->title = $data['title'];
        $pullRequest->userId = (int) $data['userId'];
        $pullRequest->body = isset($data['body']) ? $data['body'] : '';

        $pullRequest->createdAt = new DateTimeImmutable($data['createdAt']);
        $pullRequest->updatedAt = isset($data['updatedAt']) ? new DateTimeImmutable($data['updatedAt']) : null;
        $pullRequest->closedAt = isset($data['closedAt']) ? new DateTimeImmutable($data['closedAt']) : null;
        $pullRequest->mergedAt = isset($data['mergedAt']) ? new DateTimeImmutable($data['mergedAt']) : null;

        $pullRequest->mergeSha = isset($data['mergeSha']) ? $data['mergeSha'] : '';
        $pullRequest->headSha = isset($data['headSha']) ? $data['headSha'] : '';
        $pullRequest->baseSha = isset($data['baseSha']) ? $data['baseSha'] : '';

        $pullRequest->baseRef = isset($data['baseRef']) ? $data['baseRef'] : '';

        return $pullRequest;
    }

    /**
     * @param array $data
     *
     * @return GithubPullRequest
     */
    public static function createFromResponseData(array $data)
    {
        $pullRequest = new GithubPullRequest();

        $pullRequest->id = (int) $data['id'];
        $pullRequest->number = (int) $data['number'];
        $pullRequest->state = $data['state'];
        $pullRequest->title = $data['title'];
        $pullRequest->userId = (int) $data['user']['id'];
        $pullRequest->body = isset($data['body']) ? $data['body'] : '';

        $pullRequest->createdAt = new DateTimeImmutable($data['created_at']);
        $pullRequest->updatedAt = isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null;
        $pullRequest->closedAt = isset($data['closed_at']) ? new DateTimeImmutable($data['closed_at']) : null;
        $pullRequest->mergedAt = isset($data['merged_at']) ? new DateTimeImmutable($data['merged_at']) : null;

        $pullRequest->mergeSha = isset($data['merge_commit_sha']) ? $data['merge_commit_sha'] : '';
        $pullRequest->headSha = isset($data['head']['sha']) ? $data['head']['sha'] : '';
        $pullRequest->baseSha = isset($data['base']['sha']) ? $data['base']['sha'] : '';

        $pullRequest->baseRef = isset($data['base']['ref']) ? $data['base']['ref'] : '';

        return $pullRequest;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getMergedAt()
    {
        return $this->mergedAt;
    }

    /**
     * @return string
     */
    public function getMergeSha()
    {
        return $this->mergeSha;
    }

    /**
     * @return string
     */
    public function getHeadSha()
    {
        return $this->headSha;
    }

    /**
     * @return string
     */
    public function getBaseSha()
    {
        return $this->baseSha;
    }

    /**
     * @return string
     */
    public function getBaseRef()
    {
        return $this->baseRef;
    }
}
