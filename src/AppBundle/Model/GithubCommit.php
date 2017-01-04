<?php

namespace AppBundle\Model;

use DateTimeImmutable;
use DateTimeInterface;

class GithubCommit
{
    /**
     * @var string
     */
    private $sha;

    /**
     * @var string
     */
    private $committerName;

    /**
     * @var string
     */
    private $committerEmail;

    /**
     * @var DateTimeInterface
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
        $this->sha = $data['sha'];
        $this->date = new DateTimeImmutable($data['date']);
        $this->message = $data['message'];

        $this->committerId = isset($data['committer_id']) ? $data['committer_id'] : null;
        $this->committerName = isset($data['committer_name']) ? $data['committer_name'] : '';
        $this->committerEmail = isset($data['committer_email']) ? $data['committer_email'] : '';
        $this->committerLogin = isset($data['committer_login']) ? $data['committer_login'] : '';
    }

    /**
     * @param array $responseData
     *
     * @return GithubCommit
     */
    public static function createFromGithubResponseData(array $responseData)
    {
        $data = [
            'sha' => $responseData['sha'],
            'message' => $responseData['commit']['message'],
            'date' => $responseData['commit']['author']['date'],

            'committer_id' => isset($responseData['author']['id']) ? $responseData['author']['id'] : null,
            'committer_name' => isset($responseData['commit']['author']['name']) ? $responseData['commit']['author']['name'] : '',
            'committer_email' => isset($responseData['commit']['author']['email']) ? $responseData['commit']['author']['email'] : '',
            'committer_login' => isset($responseData['author']['login']) ? $responseData['author']['login'] : '',
        ];

        return new self($data);
    }    
    
    /**
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
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
     * @return DateTimeInterface
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
