<?php

namespace Aa\ATrends\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * PullRequestReview
 *
 * @ORM\Table(
 *      name="pull_request_comment",
 *      indexes={
 *          @ORM\Index(name="pull_request_comment_github_id_idx", columns={"github_id"}),
 *          @ORM\Index(name="pull_request_comment_pull_request_id_idx", columns={"pull_request_id"}),
 *      }
 * )
 * @ORM\Entity(repositoryClass="Aa\ATrends\Repository\PullRequestCommentRepository")
 */
class PullRequestComment
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
     * @ORM\Column(name="github_id", type="integer")
     */
    private $githubId;

    /**
     * @var int
     *
     * @ORM\Column(name="pull_request_id", type="integer")
     */
    private $pullRequestId;

    /**
     * @var string
     *
     * @ORM\Column(name="pull_request_review_id", type="integer", nullable=true)
     */
    private $pullRequestReviewId;

    /**
     * @var int
     *
     * @ORM\Column(name="github_user_id", type="integer")
     */
    private $githubUserId;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @return PullRequestComment
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
     * Set state
     *
     * @param string $pullRequestReviewId
     *
     * @return PullRequestComment
     */
    public function setPullRequestReviewId($pullRequestReviewId)
    {
        $this->pullRequestReviewId = $pullRequestReviewId;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getPullRequestReviewId()
    {
        return $this->pullRequestReviewId;
    }

    /**
     * Set githubUserId
     *
     * @param integer $githubUserId
     *
     * @return PullRequestComment
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
     * Set submittedAt
     *
     * @param DateTimeInterface $createdAt
     *
     * @return PullRequestComment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get submittedAt
     *
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
     * @param int $pullRequestId
     *
     * @return PullRequestComment
     */
    public function setPullRequestId($pullRequestId)
    {
        $this->pullRequestId = $pullRequestId;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     *
     * @return PullRequestComment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

