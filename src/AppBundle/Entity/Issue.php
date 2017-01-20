<?php

namespace AppBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueRepository")
 */
class Issue
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
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="github_id", type="integer", unique=true)
     */
    private $githubId;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
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
     * @var array
     *
     * @ORM\Column(name="labels", type="simple_array")
     */
    private $labels = [];


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
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set githubId
     *
     * @param integer $githubId
     *
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @return Issue
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
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }
}

