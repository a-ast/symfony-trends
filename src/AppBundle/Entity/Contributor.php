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
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, options={"default": ""})
     */
    private $country = '';

    /**
     * @var int
     *
     * @ORM\Column(name="sensiolabs_page_error", type="integer", options={"default": 0})
     */
    private $sensiolabsPageError = 0;

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
     * Add new git name
     *
     * @param string $name
     *
     * @return Contributor
     */
    public function addGitName($name)
    {
        if ($this->getName() !== $name && !in_array($name, $this->getGitNames())) {
            $this->setGitNames(ArrayUtils::trimMerge($this->getGitNames(), $name));
        }

        return $this;
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
     * @return int
     */
    public function getSensiolabsPageError()
    {
        return $this->sensiolabsPageError;
    }

    /**
     * @param int $sensiolabsPageError
     *
     * @return Contributor
     */
    public function setSensiolabsPageError($sensiolabsPageError)
    {
        $this->sensiolabsPageError = $sensiolabsPageError;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllNames()
    {
        return ArrayUtils::trimMerge($this->getName(), $this->getGitNames(), $this->getSensiolabsLogin());
    }

    /**
     * @return string[]
     */
    public function getAllEmails()
    {
        return ArrayUtils::trimMerge($this->getEmail(), $this->getGitEmails());
    }
}

