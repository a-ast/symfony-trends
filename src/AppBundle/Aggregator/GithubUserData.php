<?php


namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\GeolocationApiClient;
use AppBundle\Aggregator\Helper\GithubApiClient;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Util\StringUtils;
use Exception;

class GithubUserData implements AggregatorInterface
{

    const PROFILE_URL = 'https://github.com/';

    /**
     * @var ContributorRepository
     */
    private $repository;
    /**
     * @var GithubApiClient
     */
    private $githubApi;
    /**
     * @var GeolocationApiClient
     */
    private $geolocationApi;

    /**
     * Constructor.
     *
     * @param GithubApiClient $githubApi
     * @param GeolocationApiClient $geolocationApi
     * @param ContributorRepository $repository
     */
    public function __construct(
        GithubApiClient $githubApi,
        GeolocationApiClient $geolocationApi,
        ContributorRepository $repository
    ) {
        $this->repository = $repository;
        $this->githubApi = $githubApi;
        $this->geolocationApi = $geolocationApi;
    }


    /**
     * @param array $options
     *
     * @param ProgressInterface $progress
     *
     * @return array
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {

        $contributors = $this->repository->findWithoutLocation(3000);
        //$progress->start(count($contributors));

        $report = [
            'foundCount' => 0,
            'notFound' => [],
            'notMatched' => [],
        ];

        /** @var Contributor $contributor */
        foreach ($contributors as $contributor) {

            //$progress->advance();

            $login = $contributor->getGithubLogin();

            if ('' === $login) {
                continue;
            }

            $user = $this->githubApi->getUser($login);

            if(!isset($user['location'])) {
                continue;
            }

            $countryData = $this->geolocationApi->findCountry($user['location']);
            $country = $countryData['country'];

            if (!$countryData['exact_match']) {
                $report['notMatched'][] = sprintf('ID: %d, name: %s, github location: %s, found country: %s',
                    $contributor->getId(), $contributor->getName(), $user['location'], $country);

                continue;
            }

            if ('' === $country) {
                $report['notFound'][] = sprintf('ID: %d, name: %s, github location: %s',
                    $contributor->getId(), $contributor->getName(), $user['location']);

                continue;
            }

            $contributor->setSensiolabsCountry($country);

            $report['foundCount']++;
        }

        //$this->repository->flush();

        return $report;
    }
}
