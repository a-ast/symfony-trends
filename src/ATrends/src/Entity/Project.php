<?php

namespace Aa\ATrends\Entity;

use Aa\ATrends\Model\ProjectInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Aa\ATrends\Repository\ProjectRepository")
 */
class Project implements ProjectInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="github_path", type="string", length=255, unique=true)
     */
    private $githubPath;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=100, options={"default": ""})
     */
    private $color = '';

    /**
     * @var string
     *
     * @ORM\Column(name="contributorPageUri", type="text", options={"default": ""})
     */
    private $contributorPageUri = '';

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
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set githubPath
     *
     * @param string $githubPath
     *
     * @return Project
     */
    public function setGithubPath($githubPath)
    {
        $this->githubPath = $githubPath;

        return $this;
    }

    /**
     * Get githubPath
     *
     * @return string
     */
    public function getGithubPath()
    {
        return $this->githubPath;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Project
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Project
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getContributorPageUri()
    {
        return $this->contributorPageUri;
    }

    /**
     * @param string $contributorPageUri
     *
     * @return Project
     */
    public function setContributorPageUri($contributorPageUri)
    {
        $this->contributorPageUri = $contributorPageUri;

        return $this;
    }
}

