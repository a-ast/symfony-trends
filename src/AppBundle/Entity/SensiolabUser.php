<?php

namespace AppBundle\Entity;

use AppBundle\Traits\TimestampTrait;
use AppBundle\Util\ArrayUtils;
use Doctrine\ORM\Mapping as ORM;

/**
 * SensiolabUser
 *
 * @ORM\Table(name="sensiolab_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SensiolabUserRepository")
 */
class SensiolabUser
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
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, options={"default": ""})
     */
    private $login = '';

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, options={"default": ""})
     */
    private $country = '';

    /**
     * @var int
     *
     * @ORM\Column(name="profile_page_error", type="integer", options={"default": 0})
     */
    private $profilePageError = 0;

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
     * @return SensiolabUser
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
     * @return SensiolabUser
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
     * Set login
     *
     * @param string $login
     *
     * @return SensiolabUser
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set otherEmails
     *
     * @param array $gitEmails
     *
     * @return SensiolabUser
     */
    public function setGitEmails($gitEmails)
    {
        if (0 === count($gitEmails)) {
            $gitEmails = [''];
        }

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
     * Set country
     *
     * @param string $country
     *
     * @return SensiolabUser
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
    public function getProfilePageError()
    {
        return $this->profilePageError;
    }

    /**
     * @param int $profilePageError
     *
     * @return SensiolabUser
     */
    public function setProfilePageError($profilePageError)
    {
        $this->profilePageError = $profilePageError;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllEmails()
    {
        return ArrayUtils::trimMerge($this->getEmail(), $this->getGitEmails());
    }
}

