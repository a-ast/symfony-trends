<?php

namespace AppBundle\Model;

class GithubUser
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $email = '';

    /**
     * @var string
     */
    private $location = '';

    /**
     * @var string
     */
    private $country = '';

    /**
     * Constructor
     *
     * @param string $name
     * @param string $email
     * @param string $location
     * @param string $country
     */
    public function __construct($name = '', $email = '', $location = '', $country = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->location = $location;
        $this->country = $country;
    }

    public static function createFromArray(array $data)
    {
        return new self($data['name'], $data['email'], $data['location'], $data['country']);
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}
