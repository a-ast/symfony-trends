<?php


namespace AppBundle\Provider;

use AppBundle\Entity\ProjectVersion;
use AppBundle\Repository\ProjectVersionRepository;

class ContributorsPerVersion
{
    /**
     * @var ProjectVersionRepository
     */
    private $repository;

    /**
     * Constructor.
     * @param ProjectVersionRepository $repository
     */
    public function __construct(ProjectVersionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData(array $options = [])
    {
        /** @var ProjectVersion[] $versions */
        $versions = $this->repository->findBy(['projectId' => 1], ['label' => 'ASC']);

        $data = [];

        foreach ($versions as $version) {
            $data[] = [
                'text' => $version->getLabel(),
                'value' => $version->getContributorCount(),
            ];
        }

        return $data;
    }
}
