<?php

namespace Aa\ATrends\Api\Github\Model;

use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

class GithubIssue
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
     * @var array
     */
    private $labels;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return GithubIssue
     */
    public static function createFromArray(array $data)
    {
        $issue = new GithubIssue();

        $issue->id = (int) $data['id'];
        $issue->number = (int) $data['number'];
        $issue->state = $data['state'];
        $issue->title = $data['title'];
        $issue->userId = (int) $data['userId'];
        $issue->body = isset($data['body']) ? $data['body'] : '';

        $issue->createdAt = new DateTimeImmutable($data['createdAt']);
        $issue->updatedAt = isset($data['updatedAt']) ? new DateTimeImmutable($data['updatedAt']) : null;
        $issue->closedAt = isset($data['closedAt']) ? new DateTimeImmutable($data['closedAt']) : null;
        $issue->labels = isset($data['labels']) ? $data['labels'] : [];

        return $issue;
    }

    /**
     * @param array $data
     *
     * @return GithubIssue
     */
    public static function createFromResponseData(array $data)
    {
        if (isset($data['pull_request'])) {
            throw new RuntimeException('Response must not contain pull request data.');
        }

        $issue = new GithubIssue();

        $issue->id = (int) $data['id'];
        $issue->number = (int) $data['number'];
        $issue->state = $data['state'];
        $issue->title = $data['title'];
        $issue->userId = (int) $data['user']['id'];
        $issue->body = isset($data['body']) ? $data['body'] : '';

        $issue->createdAt = new DateTimeImmutable($data['created_at']);
        $issue->updatedAt = isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null;
        $issue->closedAt = isset($data['closed_at']) ? new DateTimeImmutable($data['closed_at']) : null;

        $issue->labels = array_column($data['labels'], 'name');

        return $issue;
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
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }
}
