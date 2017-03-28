<?php

namespace Aa\ATrends\Api\Github\Model;

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
