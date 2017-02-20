<?php

namespace AppBundle\Aggregator;

use Aa\ATrends\Entity\Contributor;
use Aa\ATrends\Repository\ContributorRepository;
use Aa\ATrends\Util\StringUtils;
use AppBundle\Api\Sensiolabs\SensiolabsApiInterface;
use AppBundle\Entity\SensiolabsUser;
use AppBundle\Repository\SensiolabsUserRepository;
use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Http\Client\Exception\HttpException;

class SensiolabsConnectAggregator implements AggregatorInterface
{
    const GITHUB_PROFILE_PAGE = 'https://github.com/';

    use ProgressNotifierAwareTrait;

    /**
     * @var SensiolabsApiInterface
     */
    private $api;

    /**
     * @var SensiolabsUserRepository
     */
    private $sensiolabsUserRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * Constructor.
     *
     * @param SensiolabsApiInterface $api
     * @param SensiolabsUserRepository $sensiolabsUserRepository
     * @param ContributorRepository $contributorRepository
     */
    public function __construct(SensiolabsApiInterface $api,
        SensiolabsUserRepository $sensiolabsUserRepository,
        ContributorRepository $contributorRepository)
    {
        $this->api = $api;
        $this->sensiolabsUserRepository = $sensiolabsUserRepository;
        $this->contributorRepository = $contributorRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options)
    {
        $report = [];

        /** @var SensiolabsUser[] $users */
        $users = $this->sensiolabsUserRepository->findBy(['contributorId' => 0]);

        foreach ($users as $user) {

            try {
                $apiUser = $this->api->getUser($user->getLogin());

                // @todo
                // 1. parse github url -> find contributor by login, store contributor_id
                // 2. extend entity, store facebook, twitter etc

                if (StringUtils::startsWith($apiUser->getGithubUrl(), self::GITHUB_PROFILE_PAGE)) {

                    $githubLogin = StringUtils::textAfter($apiUser->getGithubUrl(), self::GITHUB_PROFILE_PAGE);

                    /** @var Contributor $contributor */
                    $contributor = $this->contributorRepository->findOneBy(['githubLogin' => $githubLogin]);

                    if (null !== $contributor) {
                        $user->setContributorId($contributor->getId());
                    }
                }

                $user
                    ->setName($apiUser->getName())
                    ->setCountry($apiUser->getCountry());

            } catch (HttpException $e) {
                $user->setProfilePageError($e->getCode());

                continue;
            }
        }

        //$this->repository->flush();

        return $report;
    }
}
