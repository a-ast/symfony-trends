<?php

namespace AppBundle\Aggregator;

use AppBundle\Api\ContributorPage\ContributorPageApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\SensiolabsUser;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\ProjectInterface;
use AppBundle\Repository\SensiolabsUserRepository;

class ContributorPageAggregator implements ProjectAwareAggregatorInterface
{
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
    public function aggregate(ProjectInterface $project, array $options, ProgressInterface $progress = null)
    {
        $logins = $this->pageApi->getContributorLogins($project->getContributorPageUri(), $this->profileUri);
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
