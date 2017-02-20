<?php

namespace AppBundle\Model;

class SensiolabsUser
{
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

    public static function createFromArray(array $data)
    {
        $user = new SensiolabsUser();

        $user->name = isset($data['name']) ? $data['name'] : '';
        $user->city = isset($data['city']) ? $data['city'] : '';
        $user->country = isset($data['country']) ? $data['country'] : '';
        $user->githubUrl = isset($data['githubUrl']) ? $data['githubUrl'] : '';
        $user->facebookUrl = isset($data['facebookUrl']) ? $data['facebookUrl'] : '';
        $user->twitterUrl = isset($data['twitterUrl']) ? $data['twitterUrl'] : '';
        $user->linkedInUrl = isset($data['linkedInUrl']) ? $data['linkedInUrl'] : '';
        $user->websiteUrl = isset($data['websiteUrl']) ? $data['websiteUrl'] : '';
        $user->blogUrl = isset($data['blogUrl']) ? $data['blogUrl'] : '';
        $user->blogFeedUrl = isset($data['blogFeedUrl']) ? $data['blogFeedUrl'] : '';
        
        return $user;
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
