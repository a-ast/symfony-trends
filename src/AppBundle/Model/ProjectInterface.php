<?php

namespace AppBundle\Model;

/**
 * Project
 */
interface ProjectInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * Get githubPath
     *
     * @return string
     */
    public function getGithubPath();

    /**
     * @return string
     */
    public function getContributorPageUri();
}
