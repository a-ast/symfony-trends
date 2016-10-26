<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contribution
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
     * @var int
     *
     * @ORM\Column(name="commit_count", type="integer", options={"default": 0})
     */
    private $commitCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_commit_at", type="datetime")
     */
    private $firstCommitAt;

    /**
     * @var string
     *
     * @ORM\Column(name="first_commit_hash", type="string", length=255)
     */
    private $firstCommitHash = '';


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
     * @return Contribution
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
     * @return Contribution
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
     * Set firstCommitAt
     *
     * @param \DateTime $firstCommitAt
     *
     * @return Contribution
     */
    public function setFirstCommitAt($firstCommitAt)
    {
        $this->firstCommitAt = $firstCommitAt;

        return $this;
    }

    /**
     * Get firstCommitAt
     *
     * @return \DateTime
     */
    public function getFirstCommitAt()
    {
        return $this->firstCommitAt;
    }

    /**
     * Set firstCommitHash
     *
     * @param string $firstCommitHash
     *
     * @return Contribution
     */
    public function setFirstCommitHash($firstCommitHash)
    {
        $this->firstCommitHash = $firstCommitHash;

        return $this;
    }

    /**
     * Get firstCommitHash
     *
     * @return string
     */
    public function getFirstCommitHash()
    {
        return $this->firstCommitHash;
    }

    /**
     * @return int
     */
    public function getCommitCount()
    {
        return $this->commitCount;
    }

    /**
     * @param int $commitCount
     *
     * @return Contribution
     */
    public function setCommitCount($commitCount)
    {
        $this->commitCount = $commitCount;

        return $this;
    }
}

