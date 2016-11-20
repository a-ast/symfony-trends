<?php


namespace AppBundle\Aggregator;

use AppBundle\Entity\SensiolabsUser;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\SensiolabsUserRepository;

class SensiolabsUserUpdater implements AggregatorInterface
{
    /**
     * @var SensiolabsUserRepository
     */
    private $sensioLabsUserRepository;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * Constructor.
     *
     * @param SensiolabsUserRepository $sensiolabsUserRepository
     * @param ContributorRepository $contributorRepository
     */
    public function __construct(SensiolabsUserRepository $sensiolabsUserRepository,
        ContributorRepository $contributorRepository)
    {
        $this->sensioLabsUserRepository = $sensiolabsUserRepository;
        $this->contributorRepository = $contributorRepository;
    }


    /**
     * @param array $options
     * @param ProgressInterface $progress
     *
     * @return array
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        /** @var SensiolabsUser[] $users */
        $users = $this->sensioLabsUserRepository->findAll();

        foreach ($users as $user) {

            $found = false;

            foreach ($user->getAllEmails() as $email) {

                $contributor = $this->contributorRepository->findByEmail(strtolower($email));
                if (null !== $contributor) {
                    $found = true;

                    break;
                }
            }

            if ($found) {
                //print 'F';

                $user->setContributorId($contributor->getId());
            } else {
                print $user->getLogin().PHP_EOL;
            }
        }

        $this->sensioLabsUserRepository->flush();
    }
}
