<?php

namespace AppBundle\Entity;

use Aa\ATrends\Traits\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * SensiolabUser
 *
 * @ORM\Table(
 *      name="sensiolabs_user",
 *      indexes={
 *          @ORM\Index(
 *              name="idx_sensiolabs_user_contributor_id",
 *              columns={"contributor_id"}
 *          ),
 *          @ORM\Index(
 *              name="idx_sensiolabs_user_country",
 *              columns={"country"}
 *          )
 *      }
 *
 * )
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SensiolabsUserRepository")
 */
class SensiolabsUser
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
     * @ORM\Column(name="contributor_id", type="integer", nullable=true, unique=false)
     */
    private $contributorId = 0;

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
     * @return int
     */
    public function getContributorId()
    {
        return $this->contributorId;
    }

    /**
     * @param int $contributorId
     *
     * @return $this
     */
    public function setContributorId($contributorId)
    {
        $this->contributorId = $contributorId;

        return $this;
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
     * Set login
     *
     * @param string $login
     *
     * @return $this
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
     * @return int
     */
    public function getProfilePageError()
    {
        return $this->profilePageError;
    }

    /**
     * @param int $profilePageError
     *
     * @return $this
     */
    public function setProfilePageError($profilePageError)
    {
        $this->profilePageError = $profilePageError;

        return $this;
    }
}

