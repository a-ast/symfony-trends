<?php

namespace AppBundle\Model;

class SensiolabsUser
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */   
    private $githubUrl;

    /**
     * @var string
     */
    private $facebookUrl;

    /**
     * @var string
     */
    private $twitterUrl;

    /**
     * @var string
     */
    private $linkedInUrl;

    /**
     * @var string
     */
    private $websiteUrl;

    /**
     * @var string
     */
    private $blogUrl;

    /**
     * @var string
     */
    private $blogFeedUrl;

    /**
     * Constructor.
     * @param string $login
     * @param string $name
     * @param string $city
     * @param string $country
     * @param string $githubUrl
     * @param string $facebookUrl
     * @param string $twitterUrl
     * @param string $linkedInUrl
     * @param string $websiteUrl
     * @param string $blogUrl
     * @param string $blogFeedUrl
     */
    public function __construct($login, $name, $city, $country,
        $githubUrl, $facebookUrl, $twitterUrl, $linkedInUrl, 
        $websiteUrl, $blogUrl, $blogFeedUrl)
    {
        $this->login = $login;
        $this->name = $name;
        $this->city = $city;
        $this->country = $country;
        $this->githubUrl = $githubUrl;
        $this->facebookUrl = $facebookUrl;
        $this->twitterUrl = $twitterUrl;
        $this->linkedInUrl = $linkedInUrl;
        $this->websiteUrl = $websiteUrl;
        $this->blogUrl = $blogUrl;
        $this->blogFeedUrl = $blogFeedUrl;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getGithubUrl()
    {
        return $this->githubUrl;
    }

    /**
     * @return string
     */
    public function getFacebookUrl()
    {
        return $this->facebookUrl;
    }

    /**
     * @return string
     */
    public function getTwitterUrl()
    {
        return $this->twitterUrl;
    }

    /**
     * @return string
     */
    public function getLinkedInUrl()
    {
        return $this->linkedInUrl;
    }

    /**
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @return string
     */
    public function getBlogFeedUrl()
    {
        return $this->blogFeedUrl;
    }

    /**
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }
}
