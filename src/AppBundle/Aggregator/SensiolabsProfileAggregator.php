<?php

namespace AppBundle\Aggregator;

use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Entity\Contributor;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Repository\ContributorRepository;
use Aa\ATrends\Util\StringUtils;
use AppBundle\Api\ContributorPage\ContributorPageApiInterface;
use AppBundle\Api\Sensiolabs\SensiolabsApiInterface;
use AppBundle\Entity\SensiolabsUser;
use AppBundle\Repository\SensiolabsUserRepository;
use Aa\ATrends\Aggregator\Options\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\ProjectAwareTrait;

class SensiolabsProfileAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

    /**
     * @var ContributorPageApiInterface
     */
    private $pageApi;

    /**
     * @var SensiolabsApiInterface
     */
    private $profileApi;

    /**
     * @var SensiolabsUserRepository
     */
    private $sensiolabsUserRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var string
     */
    private $sensiolabsProfileUri;

    /**
     * @var string
     */
    private $githubProfileUri;

    /**
     * Constructor.
     *
     * @param ContributorPageApiInterface $pageApi
     * @param SensiolabsApiInterface $profileApi
     * @param SensiolabsUserRepository $sensiolabsUserRepository
     * @param ContributorRepository $contributorRepository
     * @param string $sensiolabsProfileUri
     * @param string $githubProfileUri
     */
    public function __construct(
        ContributorPageApiInterface $pageApi,
        SensiolabsApiInterface $profileApi,
        SensiolabsUserRepository $sensiolabsUserRepository,
        ContributorRepository $contributorRepository,
        $sensiolabsProfileUri, $githubProfileUri
    ) {
        $this->pageApi = $pageApi;
        $this->profileApi = $profileApi;
        $this->sensiolabsUserRepository = $sensiolabsUserRepository;
        $this->contributorRepository = $contributorRepository;
        $this->sensiolabsProfileUri = $sensiolabsProfileUri;
        $this->githubProfileUri = $githubProfileUri;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options)
    {
        $missingLogins = $this->getNewLogins();

        foreach ($missingLogins as $login) {

            $apiUser = $this->profileApi->getUser($login);

            $contributorId = $this->getContributorId($apiUser->getGithubUrl());

            if (0 === $contributorId) {
                // @todo: add to report
                continue;
            }

            $user = new SensiolabsUser();

            $user
                ->setLogin($login)

                ->setContributorId($contributorId)
                ->setName($apiUser->getName())
                ->setCountry($apiUser->getCountry())
                ->setFacebookUrl($apiUser->getFacebookUrl())
                ->setLinkedInUrl($apiUser->getLinkedInUrl())
                ->setTwitterUrl($apiUser->getTwitterUrl())
                ->setWebsiteUrl($apiUser->getWebsiteUrl())
                ->setBlogUrl($apiUser->getBlogUrl())
                ->setBlogFeedUrl($apiUser->getBlogFeedUrl())

                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            $this->sensiolabsUserRepository->persist($user);
        }

        $this->sensiolabsUserRepository->flush();

        return null;
    }

    /**
     * @return array
     */
    private function getNewLogins()
    {
        $logins = $this->pageApi->getContributorLogins($this->project->getContributorPageUri(), $this->sensiolabsProfileUri);
        $existingLogins = $this->sensiolabsUserRepository->getExistingLogins($logins);
        $missingLogins = array_diff($logins, $existingLogins);

        return $missingLogins;
    }

    private function getContributorId($githubUrl)
    {
        if (!StringUtils::startsWith($githubUrl, $this->githubProfileUri)) {
            return 0;
        }

        $githubLogin = StringUtils::textAfter($githubUrl, $this->githubProfileUri);

        /** @var Contributor $contributor */
        $contributor = $this->contributorRepository->findOneBy(['githubLogin' => $githubLogin]);

        if (null !== $contributor) {
            return $contributor->getId();
        }

        return 0;
    }
}
