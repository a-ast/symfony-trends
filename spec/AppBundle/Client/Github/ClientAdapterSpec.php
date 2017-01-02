<?php

namespace spec\AppBundle\Client\Github;

use AppBundle\Client\Github\ClientAdapter;
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

    function it_should_get_page_of_commits(Commits $commitsApi)
    {
        $commitsApi->all('valinor', 'repo', ['page' => 5, 'since' => '2015-10-21T04:29Z']);

        $this->getCommitsByPage('valinor/repo', new \DateTimeImmutable('21 Oct 2015 04:29:00 GMT'), 5);
    }

    function it_should_get_user(User $userApi)
    {
        $userApi->show('frodo');

        $this->getUser('frodo');
    }
}
