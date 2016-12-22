<?php

namespace AppBundle\Model;

class GithubCommit
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $committerName;

    /**
     * @var string
     */
    private $committerEmail;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $committerId;

    /**
     * @var string
     */
    private $committerLogin;

    /**
     * @var string
     */
    private $message;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->hash = $data['sha'];
        $this->date = new \DateTime($data['commit']['author']['date']);
        $this->message = $data['commit']['message'];

        $this->committerId = isset($data['author']['id']) ? $data['author']['id'] : null;

        $this->committerName = isset($data['commit']['author']['name']) ? $data['commit']['author']['name'] : '';
        $this->committerEmail = isset($data['commit']['author']['email']) ? $data['commit']['author']['email'] : '';
        $this->committerLogin = isset($data['author']['login']) ? $data['author']['login'] : '';
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getCommitterName()
    {
        return $this->committerName;
    }

    /**
     * @return string
     */
    public function getCommitterEmail()
    {
        return $this->committerEmail;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getCommitterId()
    {
        return $this->committerId;
    }

    /**
     * @return string
     */
    public function getCommitterLogin()
    {
        return $this->committerLogin;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
