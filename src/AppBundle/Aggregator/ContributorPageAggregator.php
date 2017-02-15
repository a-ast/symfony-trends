<?php

namespace AppBundle\Aggregator;

use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use AppBundle\Api\ContributorPage\ContributorPageApiInterface;
use AppBundle\Entity\SensiolabsUser;
use Aa\ATrends\Progress\ProgressInterface;
use AppBundle\Repository\SensiolabsUserRepository;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\ProjectAwareTrait;

class ContributorPageAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

    /**
     * @var ContributorPageApiInterface
     */
    private $pageApi;

    /**
     * @var SensiolabsUserRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $profileUri;

    /**
     * Constructor.
     *
     * @param ContributorPageApiInterface $pageApi
     * @param SensiolabsUserRepository $repository
     * @param string $profileUri
     */
    public function __construct(
        ContributorPageApiInterface $pageApi,
        SensiolabsUserRepository $repository,
        $profileUri
    ) {
        $this->repository = $repository;
        $this->profileUri = $profileUri;
        $this->pageApi = $pageApi;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(AggregatorOptionsInterface $options)
    {
        $logins = $this->pageApi->getContributorLogins($this->project->getContributorPageUri(), $this->profileUri);
        $existingLogins = $this->repository->getExistingLogins($logins);
        $missingLogins = array_diff($logins, $existingLogins);

        foreach ($missingLogins as $login) {

            $user = new SensiolabsUser();
            $user
                ->setLogin($login)
                ->setName($login)
                ->setContributorId(0)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
            ;

            $this->repository->persist($user);
        }

        $this->repository->flush();

        return null;
    }
}
