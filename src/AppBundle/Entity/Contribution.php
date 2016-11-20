<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contribution log
 *
 * @ORM\Table(name="contribution")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContributionRepository")
 */
class Contribution
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
     * @ORM\Column(name="contributor_id", type="integer")
     */
    private $contributorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="commited_at", type="datetime")
     */
    private $commitedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="commit_hash", type="string", length=255)
     */
    private $commitHash;


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
     * Set contributorId
     *
     * @param integer $contributorId
     *
     * @return $this
     */
    public function setContributorId($contributorId)
    {
        $this->contributorId = $contributorId;

        return $this;
    }

    /**
     * Get contributorId
     *
     * @return int
     */
    public function getContributorId()
    {
        return $this->contributorId;
    }

    /**
     * Set commitedAt
     *
     * @param \DateTime $commitedAt
     *
     * @return $this
     */
    public function setCommitedAt($commitedAt)
    {
        $this->commitedAt = $commitedAt;

        return $this;
    }

    /**
     * Get commitedAt
     *
     * @return \DateTime
     */
    public function getCommitedAt()
    {
        return $this->commitedAt;
    }

    /**
     * Set commitHash
     *
     * @param string $commitHash
     *
     * @return $this
     */
    public function setCommitHash($commitHash)
    {
        $this->commitHash = $commitHash;

        return $this;
    }

    /**
     * Get commitHash
     *
     * @return string
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }
}

