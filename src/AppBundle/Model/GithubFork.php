<?php

namespace AppBundle\Model;

use DateTimeImmutable;
use DateTimeInterface;

class GithubFork
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $ownerId;

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
    private $pushedAt;

    /**
     * Constructor.
     * @param $id
     * @param $ownerId
     * @param $createdAt
     * @param $updatedAt
     * @param $pushedAt
     * @internal param $data
     */
    public function __construct($id, $ownerId, $createdAt, $updatedAt, $pushedAt)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->pushedAt = $pushedAt;
    }

    public static function createFromArray(array $fork)
    {
        return new self(
            $fork['id'],
            $fork['ownerId'],
            new DateTimeImmutable($fork['createdAt']),
            new DateTimeImmutable($fork['updatedAt']),
            new DateTimeImmutable($fork['pushedAt'])
        );
    }

    public static function createFromGithubResponseData(array $fork)
    {
        return new self(
            $fork['id'],
            $fork['owner']['id'],
            new DateTimeImmutable($fork['created_at']),
            new DateTimeImmutable($fork['updated_at']),
            new DateTimeImmutable($fork['pushed_at'])
        );
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
    public function getOwnerId()
    {
        return $this->ownerId;
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
    public function getPushedAt()
    {
        return $this->pushedAt;
    }
}
