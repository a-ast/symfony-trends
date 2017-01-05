<?php

namespace spec\AppBundle\Client\Github;

use AppBundle\Client\Github\ClientAdapter;
use AppBundle\Model\GithubCommit;
use Exception;
use Github\Api\ApiInterface;
use Github\Api\Repo;
use Github\Api\Repository\Commits;
use Github\Api\User;
use Github\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ClientAdapter
 */
class ClientAdapterSpec extends ObjectBehavior
{
    function let(Client $client, Repo $repoApi, Commits $commitsApi, User $userApi)
    {
        $this->beConstructedWith($client);

        $client->api('repo')->willReturn($repoApi);
        $repoApi->commits()->willReturn($commitsApi);

        $client->api('user')->willReturn($userApi);

    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientAdapter::class);
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

        $this->getCommits('valinor/repo', new \DateTimeImmutable('21 Oct 2015 04:29 GMT'))->shouldBeGithubCommits(4);
    }

    public function getMatchers()
    {
        return [
            'beGithubCommits' => function ($subject, $count) {
                if (!$subject instanceof \Traversable) {
                    throw new Exception('Return value should be instance of \Traversable');
                }

                $commits = iterator_to_array($subject);

                foreach ($commits as $commit) {
                    if (!$commit instanceof GithubCommit) {
                        throw new Exception('Iterator element should be instance of GithubCommit');
                    }
                }

                return $count === count($commits);
            }
        ];
    }


    function it_should_fetch_user(User $userApi)
    {
        $userApi->show('frodo');

        $this->getUser('frodo');
    }
}
