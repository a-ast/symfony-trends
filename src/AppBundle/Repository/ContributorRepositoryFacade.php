<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;
use Doctrine\Common\Persistence\ObjectManager;

class ContributorRepositoryFacade
{
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @var ContributionHistoryRepository
     */
    private $contributionHistoryRepository;
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Constructor.
     *
     * @param ObjectManager $em
     * @param ContributorRepository $contributorRepository
     * @param ContributionRepository $contributionRepository
     * @param ContributionHistoryRepository $contributionHistoryRepository
     */
    public function __construct(
        ObjectManager $em,
        ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository,
        ContributionHistoryRepository $contributionHistoryRepository)
    {
        $this->em = $em;
        $this->contributorRepository = $contributorRepository;
        $this->contributionRepository = $contributionRepository;
        $this->contributionHistoryRepository = $contributionHistoryRepository;
    }

    /**
     * @param $email
     *
     * @return Contributor|null
     */
    public function findContributorByEmail($email)
    {
        return $this->contributorRepository->findByEmail($email);
    }

    public function persist($entity)
    {
        $this->em->persist($entity);
    }

    public function flush($entity = null)
    {
        $this->em->flush($entity);
    }
}
