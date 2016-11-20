<?php


namespace AppBundle\Aggregator;

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
        // TODO: Implement aggregate() method.
    }
}
