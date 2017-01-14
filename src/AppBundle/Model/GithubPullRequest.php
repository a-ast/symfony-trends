<?php

namespace AppBundle\Model;

use DateTimeImmutable;

class GithubPullRequest
{
    private $id;
    private $number;
    private $state;
    private $title;
    private $userId;
    private $body;
    private $createdAt;
    private $updatedAt;
    private $closedAt;
    private $mergedAt;
    private $mergeSha;
    private $headSha;
    private $baseSha;
    private $baseLabel;
    private $baseRef;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $data
     * @return GithubPullRequest
     */
    public static function createFromResponseData(array $data)
    {
        $pullRequest = new GithubPullRequest();

        $pullRequest->id = $data['id'];
        $pullRequest->number = $data['number'];
        $pullRequest->state = $data['state'];
        $pullRequest->title = $data['title'];
        $pullRequest->userId = $data['user']['id'];
        $pullRequest->body = $data['body'];

        $pullRequest->createdAt = new DateTimeImmutable($data['created_at']);
        $pullRequest->updatedAt = new DateTimeImmutable($data['updated_at']);
        $pullRequest->closedAt = new DateTimeImmutable($data['closed_at']);
        $pullRequest->mergedAt = new DateTimeImmutable($data['merged_at']);

        $pullRequest->mergeSha = $data['merge_commit_sha'];
        $pullRequest->headSha = $data['head']['sha'];

        $pullRequest->baseSha = $data['base']['sha'];
        $pullRequest->baseLabel = $data['base']['label'];
        $pullRequest->baseRef = $data['base']['ref'];

        return $pullRequest;
    }
}
