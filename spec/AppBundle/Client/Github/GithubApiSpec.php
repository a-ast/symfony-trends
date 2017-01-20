<?php

namespace spec\AppBundle\Client\Github;

use AppBundle\Client\Github\GithubApi;
use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubFork;
use AppBundle\Model\GithubIssue;
use AppBundle\Model\GithubPullRequest;
use AppBundle\Model\GithubUser;
use Exception;
use Github\Api\Issue;
use Github\Api\PullRequest;
use Github\Api\Repo;
use Github\Api\Repository\Commits;
use Github\Api\Repository\Forks;
use Github\Api\User;
use Github\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubApi
 */
class GithubApiSpec extends ObjectBehavior
{
    function let(Client $client,
        Repo $repoApi,
        Commits $commitsApi,
        User $userApi,
        Forks $forksApi,
        PullRequest $pullRequestApi,
        Issue $issueApi)
    {
        $this->beConstructedWith($client);

        $client->repo()->willReturn($repoApi);
        $repoApi->commits()->willReturn($commitsApi);
        $repoApi->forks()->willReturn($forksApi);

        $client->pullRequests()->willReturn($pullRequestApi);
        $client->issues()->willReturn($issueApi);

        $client->user()->willReturn($userApi);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubApi::class);
    }

    function it_should_fetch_commits(Commits $commitsApi)
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

        $this->getCommits('valinor/repo', new \DateTimeImmutable('21 Oct 2015 04:29 GMT'))->shouldBeCollectionOf(GithubCommit::class, 4);
    }

    function it_should_fetch_user(User $userApi)
    {
        $responseData = [
            'name' =>'frodo',
            'email' => 'frodo@shire',
            'location' => 'Bag End'
        ];

        $userApi->show('frodo')->willReturn($responseData);

        $user = $this->getUser('frodo');
        $user->shouldHaveType(GithubUser::class);
    }

    function it_should_fetch_pull_requests(PullRequest $pullRequestApi)
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

        $this->getPullRequests('valinor/repo')->shouldBeCollectionOf(GithubPullRequest::class, 1);
    }

    function it_should_fetch_issues(Issue $issueApi)
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

        $this->getIssues('valinor/repo')->shouldBeCollectionOf(GithubIssue::class, 1);
    }

    function it_should_fetch_issues_but_ignore_pull_requests(Issue $issueApi)
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
