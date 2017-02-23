<?php

namespace Aa\ATrends\Api\Github\Model;

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
     * Constructor.
     */
    private function __construct()
    {
    }

    public static function createFromArray(array $data)
    {
        $commit = new GithubCommit();

        $commit->sha = $data['sha'];
        $commit->date = new DateTimeImmutable($data['date']);
        $commit->message = $data['message'];

        $commit->authorId = isset($data['authorId']) ? $data['authorId'] : null;
        $commit->authorLogin = isset($data['authorLogin']) ? $data['authorLogin'] : '';
        $commit->commitAuthorName = isset($data['commitAuthorName']) ? $data['commitAuthorName'] : '';
        $commit->commitAuthorEmail = isset($data['commitAuthorEmail']) ? $data['commitAuthorEmail'] : '';

        return $commit;
    }

    /**
     * @param array $responseData
     *
     * @return GithubCommit
     */
    public static function createFromResponseData(array $responseData)
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

        return self::createFromArray($data);
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
