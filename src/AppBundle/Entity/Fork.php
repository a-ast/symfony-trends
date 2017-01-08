<?php

namespace AppBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fork
 *
 * @ORM\Table(name="fork")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ForkRepository")
 */
class Fork
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
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="owner_github_id", type="integer", nullable=true)
     */
    private $ownerGithubId;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="pushed_at", type="datetime")
     */
    private $pushedAt;

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
     * @return Fork
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
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return Fork
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
     * Set ownerGithubId
     *
     * @param integer $ownerGithubId
     *
     * @return Fork
     */
    public function setOwnerGithubId($ownerGithubId)
    {
        $this->ownerGithubId = $ownerGithubId;

        return $this;
    }

    /**
     * Get ownerGithubId
     *
     * @return int
     */
    public function getOwnerGithubId()
    {
        return $this->ownerGithubId;
    }

    /**
     * Set createdAt
     *
     * @param DateTimeInterface $createdAt
     *
     * @return Fork
     */
    public function setCreatedAt(DateTimeInterface $createdAt)
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
     * @return Fork
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt)
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
     * Set pushedAt
     *
     * @param DateTimeInterface $pushedAt
     *
     * @return Fork
     */
    public function setPushedAt(DateTimeInterface $pushedAt)
    {
        $this->pushedAt = $pushedAt;

        return $this;
    }

    /**
     * Get pushedAt
     *
     * @return DateTimeInterface
     */
    public function getPushedAt()
    {
        return $this->pushedAt;
    }
}

