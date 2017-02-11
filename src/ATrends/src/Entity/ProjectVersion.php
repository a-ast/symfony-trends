<?php

namespace Aa\ATrends\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectVersion
 *
 * @ORM\Table(name="project_version")
 * @ORM\Entity(repositoryClass="Aa\ATrends\Repository\ProjectVersionRepository")
 */
class ProjectVersion
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
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, options={"default": ""})
     */
    private $label = '';

    /**
     * @var int
     *
     * @ORM\Column(name="contributor_count", type="integer", options={"default": 0})
     */
    private $contributorCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="contributor_count2", type="integer", options={"default": 0})
     */
    private $contributorCount2 = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="released_at", type="datetime", nullable=true)
     */
    private $releasedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;

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
     * @return ProjectVersion
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
     * Set label
     *
     * @param string $label
     *
     * @return ProjectVersion
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set contributorCount
     *
     * @param int $contributorCount
     *
     * @return ProjectVersion
     */
    public function setContributorCount($contributorCount)
    {
        $this->contributorCount = $contributorCount;

        return $this;
    }

    /**
     * Get contributorCount
     *
     * @return int
     */
    public function getContributorCount()
    {
        return $this->contributorCount;
    }

    /**
     * Set contributorCount
     *
     * @param int $contributorCount
     *
     * @return ProjectVersion
     */
    public function setContributorCount2($contributorCount)
    {
        $this->contributorCount2 = $contributorCount;

        return $this;
    }

    /**
     * Get contributorCount
     *
     * @return int
     */
    public function getContributorCount2()
    {
        return $this->contributorCount2;
    }

    /**
     * @return \DateTime
     */
    public function getReleasedAt()
    {
        return $this->releasedAt;
    }

    /**
     * @param \DateTime $releasedAt
     *
     * @return ProjectVersion
     */
    public function setReleasedAt($releasedAt)
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime $startedAt
     *
     * @return ProjectVersion
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }
}

