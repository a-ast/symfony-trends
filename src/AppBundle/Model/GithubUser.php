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
     * Constructor
     *
     * @param string $name
     * @param string $email
     * @param string $location
     */
    public function __construct($name, $email = '', $location = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->location = $location;
    }

    public static function createFromGithubResponseData(array $responseData)
    {
        return new self(
            $responseData['name'],
            isset($responseData['email']) ? $responseData['email'] : '',
            isset($responseData['location']) ? $responseData['location'] : ''
        );
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
}
