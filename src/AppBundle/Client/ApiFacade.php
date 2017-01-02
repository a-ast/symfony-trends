<?php

namespace AppBundle\Client;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Model\GithubUser;

class ApiFacade
{
    /**
     * @var ClientAdapterInterface
     */
    private $githubApi;
    /**
     * @var GeolocationApiClient
     */
    private $geoApi;

    /**
     * Constructor.
     *
     * @param ClientAdapterInterface $githubApi
     * @param GeolocationApiClient $geoApi
     */
    public function __construct(ClientAdapterInterface $githubApi, GeolocationApiClient $geoApi)
    {
        $this->githubApi = $githubApi;
        $this->geoApi = $geoApi;
    }

    /**
     * @param string $login
     *
     * @return GithubUser
     */
    public function getGithubUserWithLocation($login)
    {
        $user = $this->githubApi->getUser($login);

        $location = $country = '';

        $name = isset($user['name']) ? $user['name'] : '';
        $email = isset($user['email']) ? $user['email'] : '';

        if (isset($user['location'])) {
            $location = $user['location'];
            $countryData = $this->geoApi->findCountry($location);
            $country = $countryData['country'];
        }

        return new GithubUser($name, $email, $location, $country);
    }
}
