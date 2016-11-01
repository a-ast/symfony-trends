<?php

namespace AppBundle\Entity;

use AppBundle\Traits\TimestampTrait;
use AppBundle\Util\ArrayUtils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contributor
 *
 * @ORM\Table(name="contributor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContributorRepository")
 */
class Contributor
{
    use TimestampTrait;

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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="git_emails", type="simple_array")
     */
    private $gitEmails = [];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="git_names", type="simple_array")
     */
    private $gitNames = [];

    /**
     * @var string
     *
     * @ORM\Column(name="sensiolabs_login", type="string", length=255, options={"default": ""})
     */
    private $sensiolabsLogin = '';

    /**
     * @var string
     *
     * @ORM\Column(name="github_login", type="string", length=255, options={"default": ""})
     */
    private $githubLogin = '';

    /**
     * @var int
     *
     * @ORM\Column(name="github_id", type="integer", options={"default": 0})
     */
    private $githubId = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, options={"default": ""})
     */
    private $country = '';

    /**
     * @var array
     *
     * @ORM\Column(name="countries", type="simple_array", options={"default": ""})
     */
    private $countries = [];

    /**
     * @var int
     *
     * @ORM\Column(name="commit_count", type="integer", options={"default": 0})
     */
    private $commitCount = 0;

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
     * Set email
     *
     * @param string $email
     *
     * @return Contributor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Contributor
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
     * Set otherNames
     *
     * @param array $gitNames
     *
     * @return Contributor
     */
    public function setGitNames($gitNames)
    {
        $this->gitNames = $gitNames;

        return $this;
    }

    /**
     * Get otherNames
     *
     * @return array
     */
    public function getGitNames()
    {
        return $this->gitNames;
    }

    /**
     * Set sensiolabsLogin
     *
     * @param string $sensiolabsLogin
     *
     * @return Contributor
     */
    public function setSensiolabsLogin($sensiolabsLogin)
    {
        $this->sensiolabsLogin = $sensiolabsLogin;

        return $this;
    }

    /**
     * Get sensiolabsLogin
     *
     * @return string
     */
    public function getSensiolabsLogin()
    {
        return $this->sensiolabsLogin;
    }

    /**
     * Set otherEmails
     *
     * @param array $gitEmails
     *
     * @return Contributor
     */
    public function setGitEmails($gitEmails)
    {
        $this->gitEmails = $gitEmails;

        return $this;
    }

    /**
     * Get otherEmails
     *
     * @return array
     */
    public function getGitEmails()
    {
        return $this->gitEmails;
    }

    /**
     * Set githubLogin
     *
     * @param string $githubLogin
     *
     * @return Contributor
     */
    public function setGithubLogin($githubLogin)
    {
        $this->githubLogin = $githubLogin;

        return $this;
    }

    /**
     * Get githubLogin
     *
     * @return string
     */
    public function getGithubLogin()
    {
        return $this->githubLogin;
    }

    /**
     * Set githubId
     *
     * @param integer $githubId
     *
     * @return Contributor
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
     * Set country
     *
     * @param string $country
     *
     * @return Contributor
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set countries
     *
     * @param array $countries
     *
     * @return Contributor
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * Get countries
     *
     * @return array
     */
    public function getCountries()
    {
        return $this->countries;
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
     * @return Contributor
     */
    public function setCommitCount($commitCount)
    {
        $this->commitCount = $commitCount;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllNames()
    {
        return ArrayUtils::trimMerge($this->getName(), $this->getGitNames());
    }
}
