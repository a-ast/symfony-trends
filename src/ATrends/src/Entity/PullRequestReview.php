<?php

namespace Aa\ATrends\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * PullRequestReview
 *
 * @ORM\Table(
 *      name="pull_request_review",
 *      indexes={
 *          @ORM\Index(name="pull_request_review_github_id_idx", columns={"github_id"}),
 *          @ORM\Index(name="pull_request_review_pull_request_id_idx", columns={"pull_request_id"}),
 *      }
 * )
 * @ORM\Entity(repositoryClass="Aa\ATrends\Repository\PullRequestReviewRepository")
 */
class PullRequestReview
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
     * @ORM\Column(name="state", type="text")
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="github_user_id", type="integer")
     */
    private $githubUserId;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="submitted_at", type="datetime", nullable=true)
     */
    private $submittedAt;

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
     * @return PullRequestReview
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
     * @param string $state
     *
     * @return PullRequestReview
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
     * Set githubUserId
     *
     * @param integer $githubUserId
     *
     * @return PullRequestReview
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
     * @param DateTimeInterface $submittedAt
     *
     * @return PullRequestReview
     */
    public function setSubmittedAt($submittedAt)
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    /**
     * Get submittedAt
     *
     * @return DateTimeInterface
     */
    public function getSubmittedAt()
    {
        return $this->submittedAt;
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
     * @return PullRequestReview
     */
    public function setPullRequestId($pullRequestId)
    {
        $this->pullRequestId = $pullRequestId;

        return $this;
    }
}

