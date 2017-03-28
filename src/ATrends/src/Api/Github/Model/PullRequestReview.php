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
