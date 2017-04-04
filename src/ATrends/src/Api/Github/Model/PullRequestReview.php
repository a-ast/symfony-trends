<?php

namespace Aa\ATrends\Api\Github\Model;

use DateTimeImmutable;

class PullRequestReview
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $state;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var DateTimeInterface
     */
    private $submittedAt;

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
        $pullRequest = new PullRequestReview();

        $pullRequest->id = (int)$data['id'];
        $pullRequest->state = $data['state'];
        $pullRequest->userId = (int)$data['userId'];
        $pullRequest->submittedAt = new DateTimeImmutable($data['submittedAt']);

        return $pullRequest;
    }

    /**
     * @param array $data
     *
     * @return PullRequestReview
     */
    public static function createFromResponseData(array $data)
    {
        $pullRequest = new PullRequestReview();

        $pullRequest->id = (int)$data['id'];
        $pullRequest->state = $data['state'];
        $pullRequest->userId = (int)$data['user']['id'];
        $pullRequest->submittedAt = new DateTimeImmutable($data['submitted_at']);

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
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
