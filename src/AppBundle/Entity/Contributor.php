<?php

namespace AppBundle\Entity;

use Aa\ATrends\Model\GithubCommit;
use Aa\ATrends\Model\GithubUser;
use AppBundle\Traits\TimestampTrait;
use Aa\ATrends\Util\ArrayUtils;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contributor
 *
 * @ORM\Table(
 *      name="contributor",
 *      indexes={
 *          @ORM\Index(
 *              name="idx_contributor_country",
 *              columns={"country"}
 *          )
 *      }
 * )
 *
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
     * @var int
     *
     * @ORM\Column(name="github_id", type="integer", nullable=true)
     */
    private $githubId;

    /**
     * @var string
     *
     * @ORM\Column(name="github_login", type="text", options={"default": ""})
     */
    private $githubLogin = '';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", unique=true)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="git_emails", type="simple_array", options={"default": ""})
     */
    private $gitEmails = [];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
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
     * @ORM\Column(name="country", type="text", options={"default": ""})
     */
    private $country = '';

    /**
     * @var string
     *
     * @ORM\Column(name="github_location", type="text", options={"default": ""})
     */
    private $githubLocation = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="is_ignored_location", type="boolean", options={"default": "0"})
     */
    private $isIgnoredLocation = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_core_member", type="boolean", options={"default": "0"})
     */
    private $isCoreMember = false;

    /**
     * Constructor.
     *
     * @param string $email
     */
    public function __construct($email = '')
    {
        $this->email = $email;

        $this->gitEmails = [];
        $this->gitNames = [];

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * @return string
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @param int $githubId
     *
     * @return $this
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
        return ArrayUtils::trim($this->gitNames);
    }

    /**
     * Add new git name
     *
     * @param string $name
     *
     * @return $this
     */
    public function addGitName($name)
    {
        if (!in_array($name, $this->getAllNames())) {
            $this->setGitNames(ArrayUtils::trimMerge($this->getGitNames(), $name));
        }

        return $this;
    }

    /**
     * Set gitEmails
     *
     * @param array $gitEmails
     *
     * @return $this
     */
    public function setGitEmails($gitEmails)
    {
        $this->gitEmails = $gitEmails;

        return $this;
    }

    /**
     * Get gitEmails
     *
     * @return array
     */
    public function getGitEmails()
    {
        return ArrayUtils::trim($this->gitEmails);
    }

    /**
     * Add new git email
     *
     * @param string $email
     *
     * @return $this
     */
    public function addGitEmail($email)
    {
        if (!in_array($email, $this->getAllEmails())) {
            $this->setGitEmails(ArrayUtils::trimMerge($this->getGitEmails(), $email));
        }

        return $this;
    }    
    
    /**
     * Set githubLogin
     *
     * @param string $githubLogin
     *
     * @return $this
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
     * @return $this
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
     * @return string[]
     */
    public function getAllNames()
    {
        return ArrayUtils::trimMerge($this->getGithubLogin(), $this->getName(), $this->getGitNames());
    }

    /**
     * @return string[]
     */
    public function getAllEmails()
    {
        return ArrayUtils::trimMerge($this->getEmail(), $this->getGitEmails());
    }

    /**
     * @return string
     */
    public function getGithubLocation()
    {
        return $this->githubLocation;
    }

    /**
     * @param string $githubLocation
     *
     * @return $this;
     */
    public function setGithubLocation($githubLocation)
    {
        $this->githubLocation = $githubLocation;

        return $this;
    }

    /**
     * @param boolean $isIgnoredLocation
     *
     * @return $this
     */
    public function setIsIgnoredLocation($isIgnoredLocation)
    {
        $this->isIgnoredLocation = $isIgnoredLocation;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIgnoredLocation()
    {
        return $this->isIgnoredLocation;
    }

    /**
     * @param boolean $isCoreMember
     *
     * @return $this
     */
    public function setCoreMember($isCoreMember)
    {
        $this->isCoreMember = $isCoreMember;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCoreMember()
    {
        return $this->isCoreMember;
    }

    /**
     * @param GithubCommit $commit
     *
     * @return $this
     */
    public function setFromGithubCommit(GithubCommit $commit)
    {
        if (!$this->githubLogin) {
            $this->setGithubLogin($commit->getAuthorLogin());
        }

        if (!$this->githubId) {
            $this->setGithubId($commit->getAuthorId());
        }

        if (!$this->getName()) {
            $this->setName($commit->getCommitAuthorName());
        }

        $this->addGitName($commit->getCommitAuthorName());
        $this->addGitName($commit->getAuthorLogin());
        $this->addGitEmail($commit->getCommitAuthorEmail()); // in case it is not added yet

        return $this;
    }

    /**
     * @param GithubUser $user
     *
     * @return $this
     */
    public function setFromGithubUser(GithubUser $user)
    {
        if (!$this->getName()) {
            $this->setName($user->getName());
        }

        if (!$this->getGithubLocation()) {
            $this->setGithubLocation($user->getLocation());
        }

        $this->addGitName($user->getName());
        $this->addGitEmail($user->getEmail());

        return $this;
    }

    /**
     * @param GithubCommit $commit
     * @param GithubUser|null $user
     *
     * @return $this
     */
    public function setFromGithub(GithubCommit $commit, GithubUser $user = null)
    {
        if (null !== $user) {
            $this->setFromGithubUser($user);
        }
        $this->setFromGithubCommit($commit);

        return $this;
    }
}

