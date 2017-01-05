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
    private $commitAuthorName;

    /**
     * @var string
     */
    private $commitAuthorEmail;

    /**
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var int
     */
    private $authorId;

    /**
     * @var string
     */
    private $authorLogin;

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

        $this->authorId = isset($data['authorId']) ? $data['authorId'] : null;
        $this->authorLogin = isset($data['authorLogin']) ? $data['authorLogin'] : '';
        $this->commitAuthorName = isset($data['commitAuthorName']) ? $data['commitAuthorName'] : '';
        $this->commitAuthorEmail = isset($data['commitAuthorEmail']) ? $data['commitAuthorEmail'] : '';
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

            'commitAuthorName' => isset($responseData['commit']['author']['name']) ? $responseData['commit']['author']['name'] : '',
            'commitAuthorEmail' => isset($responseData['commit']['author']['email']) ? $responseData['commit']['author']['email'] : '',

            'authorId' => isset($responseData['author']['id']) ? $responseData['author']['id'] : null,
            'authorLogin' => isset($responseData['author']['login']) ? $responseData['author']['login'] : '',
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
    public function getCommitAuthorName()
    {
        return $this->commitAuthorName;
    }

    /**
     * @return string
     */
    public function getCommitAuthorEmail()
    {
        return $this->commitAuthorEmail;
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
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @return string
     */
    public function getAuthorLogin()
    {
        return $this->authorLogin;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
