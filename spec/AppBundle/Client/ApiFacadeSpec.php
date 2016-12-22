<?php

namespace spec\AppBundle\Client;

use AppBundle\Client\ApiFacade;
use AppBundle\Client\GeolocationApiClient;
use AppBundle\Client\GithubApiClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ApiFacade
 */
class ApiFacadeSpec extends ObjectBehavior
{
    function let(GithubApiClient $githubApi, GeolocationApiClient $geoApi)
    {
        $this->beConstructedWith($githubApi, $geoApi);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApiFacade::class);
    }

    function it_returns_github_user_with_location(GithubApiClient $githubApi, GeolocationApiClient $geoApi)
    {
        $githubApi
            ->getUser(Argument::type('string'))
            ->willReturn(['name' => 'Frodo Baggins', 'email' => 'frodo@shire', 'location' => 'Bag End',]);

        $geoApi
            ->findCountry(Argument::type('string'))
            ->willReturn(['country' => 'Shire']);

        $user = $this->getGithubUserWithLocation('frodo');
        $user->getName()->shouldBe('Frodo Baggins');
        $user->getEmail()->shouldBe('frodo@shire');
        $user->getLocation()->shouldBe('Bag End');
        $user->getCountry()->shouldBe('Shire');
    }

    function it_does_not_return_github_user_country_if_no_location_specified(GithubApiClient $githubApi, GeolocationApiClient $geoApi)
    {
        $githubApi
            ->getUser(Argument::type('string'))
            ->willReturn(['name' => 'Frodo Baggins', 'email' => 'frodo@shire']);

        $geoApi
            ->findCountry(Argument::type('string'))
            ->shouldNotBeCalled();

        $user = $this->getGithubUserWithLocation('frodo');
        $user->getCountry()->shouldBe('');
    }
}
