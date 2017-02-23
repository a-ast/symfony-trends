<?php

namespace spec\Aa\ATrends\Api\Github;

use Aa\ATrends\Api\Github\GithubApi;
use Aa\ATrends\Api\Github\Model\Commit;
use Aa\ATrends\Api\Github\Model\Issue;
use Aa\ATrends\Api\Github\Model\PullRequest;
use Aa\ATrends\Api\Github\Model\User;
use Github\Api\User as UserApi;
use Github\Api\PullRequest as PullRequestApi;
use Github\Api\Issue as IssueApi;
use Github\Api\Repository\Commits as CommitApi;
use Github\Api\Repo as RepoApi;
use Github\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Exception;

/**
 * @mixin GithubApi
 */
class GithubApiSpec extends ObjectBehavior
{
    function let(Client $client,
        RepoApi $repoApi,
        CommitApi $commitsApi,
        UserApi $userApi,
        PullRequestApi $pullRequestApi,
        IssueApi $issueApi)
    {
        $this->beConstructedWith($client);

        $client->repo()->willReturn($repoApi);
        $repoApi->commits()->willReturn($commitsApi);

        $client->pullRequests()->willReturn($pullRequestApi);
        $client->issues()->willReturn($issueApi);

        $client->user()->willReturn($userApi);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubApi::class);
    }

    function it_should_fetch_commits(CommitApi $commitsApi)
    {
        $githubResponseData = [
            0 => [
                'sha' => 'frodo-1',
                'commit' => [
                    'author' => [ 'name' => 'Frodo', 'email' => 'frodo@shire', 'date' => '2015-10-21T04:29:01Z',],
                    'message' => 'Go back, Sam. I\'m going to Mordor alone.',
                ],
                'author' => [ 'id' => 300, 'login' => 'frodo'],
            ],
            1 => [
                'sha' => 'sam-1',
                'commit' => [
                    'author' => [ 'name' => 'Sam', 'email' => 'sam@shire', 'date' => '2015-10-21T04:29:02Z',],
                    'message' => 'No!',
                ],
                'author' => [ 'id' => 400, 'login' => 'sam'],
            ],
        ];

        $commitsApi
            ->all('valinor', 'repo', ['page' => 1, 'since' => '2015-10-21T04:29:00Z'])
            ->willReturn($githubResponseData)
            ;
        $commitsApi
            ->all('valinor', 'repo', ['page' => 2, 'since' => '2015-10-21T04:29:00Z'])
            ->willReturn($githubResponseData)
            ;
        $commitsApi
            ->all('valinor', 'repo', ['page' => 3, 'since' => '2015-10-21T04:29:00Z'])
            ->willReturn([])
            ;

        $this->getCommits('valinor/repo', new \DateTimeImmutable('21 Oct 2015 04:29 GMT'))->shouldBeCollectionOf(Commit::class, 4);
    }

    function it_should_fetch_user(UserApi $userApi)
    {
        $responseData = [
            'name' =>'frodo',
            'email' => 'frodo@shire',
            'location' => 'Bag End'
        ];

        $userApi->show('frodo')->willReturn($responseData);

        $user = $this->getUser('frodo');
        $user->shouldHaveType(User::class);
    }

    function it_should_fetch_pull_requests(PullRequestApi $pullRequestApi)
    {
        $responseData = [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I will take the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...though I do not know the way.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'merged_at' => '2010-01-04T00:00:00Z',
            'merge_commit_sha' => 'xxx',
            'head' => [
                'sha' => 'yyy',
            ],
            'base' => [
                'label' => 'Ring:1.0',
                'ref' => '1.0',
                'sha' => 'zzz',
            ],
        ];

        $pullRequestApi
            ->all('valinor', 'repo', Argument::type('array'))
            ->willReturn([$responseData], []);

        $this->getPullRequests('valinor/repo')->shouldBeCollectionOf(PullRequest::class, 1);
    }

    function it_should_fetch_issues(IssueApi $issueApi)
    {
        $responseData = [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I will take the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...though I do not know the way.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'labels' => [
                ['id' => 1, 'name' => 'Bug'],
                ['id' => 2, 'name' => 'Feature'],
            ]
        ];

        $issueApi
            ->all('valinor', 'repo', Argument::type('array'))
            ->willReturn([$responseData], []);

        $this->getIssues('valinor/repo')->shouldBeCollectionOf(Issue::class, 1);
    }

    function it_should_fetch_issues_but_ignore_pull_requests(IssueApi $issueApi)
    {
        $responseData = [
            'id' => 100,
            'number' => 200,
            'state' => 'closed',
            'title' => '[Ring] I will take the Ring...',
            'user' => [
                'id' => 200,
            ],
            'body' => '...though I do not know the way.',
            'created_at' => '2010-01-01T00:00:00Z',
            'updated_at' => '2010-01-02T00:00:00Z',
            'closed_at' => '2010-01-03T00:00:00Z',
            'labels' => [
                ['id' => 1, 'name' => 'Bug'],
                ['id' => 2, 'name' => 'Feature'],
            ],
            'pull_request' => []
        ];

        $issueApi
            ->all('valinor', 'repo', Argument::type('array'))
            ->willReturn([$responseData], []);

        $this->getIssues('valinor/repo')->shouldHaveCount(0);
    }

    public function getMatchers()
    {
        return [
            'beCollectionOf' =>
                function ($subject, $class, $count) {
                    if (!$subject instanceof \Traversable) {
                        throw new Exception('Return value should be instance of \Traversable');
                    }

                    $commits = iterator_to_array($subject);

                    foreach ($commits as $commit) {
                        if (!$commit instanceof $class) {
                            throw new Exception(sprintf('Iterator element should be instance of %s', $class));
                        }
                    }

                    return $count === count($commits);
                }
        ];
    }

}
