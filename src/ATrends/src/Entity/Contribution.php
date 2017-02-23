<?php

namespace Aa\ATrends\Entity;

use Aa\ATrends\Api\Github\Model\GithubCommit;
use Aa\ATrends\Util\RegexUtils;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contribution log
 *
 * @ORM\Table(
 *      name="contribution",
 *
 *      indexes={
 *          @ORM\Index(
 *              name="idx_project_id",
 *              columns={"project_id"}
 *          ),
 *          @ORM\Index(
 *              name="idx_contributor_id",
 *              columns={"contributor_id"}
 *          )
 *      },
 *
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uidx_sha",columns={"commit_hash"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Aa\ATrends\Repository\ContributionRepository")
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
     * @var string
     *
     * @ORM\Column(name="message", type="text", options={"default": ""})
     */
    private $message = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="is_maintenance_commit", type="boolean", options={"default": false})
     */
    private $isMaintenanceCommit = false;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="commited_at", type="datetimetz")
     */
    private $commitedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="commit_hash", type="string", length=255)
     */
    private $commitHash;

    /**
     * Constructor.
     *
     * @param int $projectId
     * @param int $contributorId
     * @param string $commitHash
     */
    public function __construct($projectId, $contributorId, $commitHash)
    {
        $this->projectId = $projectId;
        $this->commitHash = $commitHash;
        $this->contributorId = $contributorId;
    }

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
     * @param DateTimeInterface $commitedAt
     *
     * @return $this
     */
    public function setCommitedAt(DateTimeInterface $commitedAt)
    {
        $this->commitedAt = $commitedAt;

        return $this;
    }

    /**
     * Get commitedAt
     *
     * @return DateTimeInterface
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

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param boolean $isMaintenanceCommit
     *
     * @return $this
     */
    public function setMaintenanceCommit($isMaintenanceCommit)
    {
        $this->isMaintenanceCommit = $isMaintenanceCommit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMaintenanceCommit()
    {
        return $this->isMaintenanceCommit;
    }

    /**
     * @param GithubCommit $commit
     * @param array $maintenanceCommitPatterns
     *
     * @return $this
     */
    public function setFromGithubCommit(GithubCommit $commit, array $maintenanceCommitPatterns)
    {
        $isMaintenanceCommit = RegexUtils::match($commit->getMessage(), $maintenanceCommitPatterns);

        return $this
            ->setMessage($commit->getMessage())
            ->setMaintenanceCommit($isMaintenanceCommit)
            ->setCommitedAt($commit->getDate());
    }
}

