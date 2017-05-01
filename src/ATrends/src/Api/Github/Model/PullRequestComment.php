<?php

namespace Aa\ATrends\Api\Github\Model;

use Aa\ATrends\Util\StringUtils;
use DateTimeImmutable;
use DateTimeInterface;

class PullRequestComment
{
    const GITHUB_API_PULLS_URL = 'https://api.github.com/repos/symfony/symfony/pulls/';
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $pullRequestId;

    /**
     * @var int
     */
    private $pullRequestReviewId;

    /**
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     */
    private $updatedAt;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return PullRequestReview
     */
    public static function createFromArray(array $data)
    {
        $comment = new self();

        $comment->id = (int)$data['id'];
        $comment->userId = (int)$data['userId'];
        $comment->pullRequestId = (int)StringUtils::textAfter($data['pull_request_id'], self::GITHUB_API_PULLS_URL);

        $comment->createdAt = new DateTimeImmutable($data['createdAt']);
        $comment->updatedAt = new DateTimeImmutable($data['updatedAt']);

        return $comment;
    }

    /**
     * @param array $data
     *
     * @return PullRequestReview
     */
    public static function createFromResponseData(array $data)
    {
        $comment = new self();

        $comment->id = (int)$data['id'];
        $comment->userId = (int)$data['user']['id'];
        $comment->pullRequestId = (int)StringUtils::textAfter($data['pull_request_id'], self::GITHUB_API_PULLS_URL);
        $comment->pullRequestReviewId = $data['pull_request_review_id'];

        $comment->createdAt = new DateTimeImmutable($data['created_at']);
        $comment->updatedAt = new DateTimeImmutable($data['updated_at']);

        return $comment;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getPullRequestId()
    {
        return $this->pullRequestId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getPullRequestReviewId()
    {
        return $this->pullRequestReviewId;
    }
}
