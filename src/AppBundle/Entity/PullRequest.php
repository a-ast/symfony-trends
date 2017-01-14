<?php

namespace AppBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * PullRequest
 *
 * @ORM\Table(name="pull_request")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PullRequestRepository")
 */
class PullRequest
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="github_id", type="integer", unique=true)
     */
    private $githubId;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", unique=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="github_user_id", type="integer")
     */
    private $githubUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="merged_at", type="datetime", nullable=true)
     */
    private $mergedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="merge_sha", type="text", nullable=true)
     */
    private $mergeSha;

    /**
     * @var string
     *
     * @ORM\Column(name="head_sha", type="text", nullable=true)
     */
    private $headSha;

    /**
     * @var string
     *
     * @ORM\Column(name="base_sha", type="text", nullable=true)
     */
    private $baseSha;

    /**
     * @var string
     *
     * @ORM\Column(name="base_label", type="text", nullable=true)
     */
    private $baseLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="base_ref", type="text", nullable=true)
     */
    private $baseRef;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set githubId
     *
     * @param integer $githubId
     *
     * @return PullRequest
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;

        return $this;
    }

    /**
     * Get githubId
     *
     * @return int
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return PullRequest
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return PullRequest
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return PullRequest
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set githubUserId
     *
     * @param integer $githubUserId
     *
     * @return PullRequest
     */
    public function setGithubUserId($githubUserId)
    {
        $this->githubUserId = $githubUserId;

        return $this;
    }

    /**
     * Get githubUserId
     *
     * @return int
     */
    public function getGithubUserId()
    {
        return $this->githubUserId;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return PullRequest
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @param DateTimeInterface $createdAt
     *
     * @return PullRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTimeInterface $updatedAt
     *
     * @return PullRequest
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set closedAt
     *
     * @param DateTimeInterface $closedAt
     *
     * @return PullRequest
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get closedAt
     *
     * @return DateTimeInterface
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Set mergedAt
     *
     * @param DateTimeInterface $mergedAt
     *
     * @return PullRequest
     */
    public function setMergedAt($mergedAt)
    {
        $this->mergedAt = $mergedAt;

        return $this;
    }

    /**
     * Get mergedAt
     *
     * @return DateTimeInterface
     */
    public function getMergedAt()
    {
        return $this->mergedAt;
    }

    /**
     * Set mergeSha
     *
     * @param string $mergeSha
     *
     * @return PullRequest
     */
    public function setMergeSha($mergeSha)
    {
        $this->mergeSha = $mergeSha;

        return $this;
    }

    /**
     * Get mergeSha
     *
     * @return string
     */
    public function getMergeSha()
    {
        return $this->mergeSha;
    }

    /**
     * Set headSha
     *
     * @param string $headSha
     *
     * @return PullRequest
     */
    public function setHeadSha($headSha)
    {
        $this->headSha = $headSha;

        return $this;
    }

    /**
     * Get headSha
     *
     * @return string
     */
    public function getHeadSha()
    {
        return $this->headSha;
    }

    /**
     * Set baseSha
     *
     * @param string $baseSha
     *
     * @return PullRequest
     */
    public function setBaseSha($baseSha)
    {
        $this->baseSha = $baseSha;

        return $this;
    }

    /**
     * Get baseSha
     *
     * @return string
     */
    public function getBaseSha()
    {
        return $this->baseSha;
    }

    /**
     * Set baseLabel
     *
     * @param string $baseLabel
     *
     * @return PullRequest
     */
    public function setBaseLabel($baseLabel)
    {
        $this->baseLabel = $baseLabel;

        return $this;
    }

    /**
     * Get baseLabel
     *
     * @return string
     */
    public function getBaseLabel()
    {
        return $this->baseLabel;
    }

    /**
     * Set baseRef
     *
     * @param string $baseRef
     *
     * @return PullRequest
     */
    public function setBaseRef($baseRef)
    {
        $this->baseRef = $baseRef;

        return $this;
    }

    /**
     * Get baseRef
     *
     * @return string
     */
    public function getBaseRef()
    {
        return $this->baseRef;
    }
}

