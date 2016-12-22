<?php

namespace AppBundle\Builder;

use AppBundle\Client\ApiFacade;
use AppBundle\Factory\ContributorFactory;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Util\ArrayUtils;

class ContributorBuilder
{
    /**
     * @var ContributorRepository
     */
    private $repository;

    /**
     * @var ContributorFactory
     */
    private $factory;

    /**
     * @var ApiFacade
     */
    private $apiFacade;

    public function __construct(ContributorRepository $repository, ContributorFactory $factory, ApiFacade $apiFacade)
    {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->apiFacade = $apiFacade;
    }

    public function buildFromGithubCommit(GithubCommit $commit)
    {
        $contributor = null;
        $user = null;
        $userEmails = [$commit->getCommitterEmail()];

        if (null !== $commit->getCommitterId()) {
            $contributor = $this->repository->findByGithubId($commit->getCommitterId());
        }

        // if contributor is not found or github id is not set,
        // but login is present
        if ((null === $contributor || null === $contributor->getGithubId()) && '' !== $commit->getCommitterLogin()) {
            $user = $this->apiFacade->getGithubUserWithLocation($commit->getCommitterLogin());

            if (null !== $user) {
                array_unshift($userEmails, $user->getEmail());
            }
        }

        // if contributor not found by id, try to find it by email
        if (null === $contributor) {
            $contributor = $this->repository->findByEmails($userEmails);
        }

        $email = ArrayUtils::getFirstNonEmptyElement($userEmails);

        if (null === $contributor) {
            $contributor = $this->factory->createFromEmail($email);
        }
        $contributor->setFromGithub($commit, $user);
        $this->repository->saveContributor($contributor);

        return $contributor;
    }
}
