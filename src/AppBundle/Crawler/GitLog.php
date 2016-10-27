<?php


namespace AppBundle\Crawler;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;

class GitLog implements CrawlerInterface
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
     * Constructor.
     *
     * @param ContributorRepository $contributorRepository
     * @param ContributionRepository $contributionRepository
     * @param string $rootDir
     */
    public function __construct(ContributorRepository $contributorRepository,
        ContributionRepository $contributionRepository, $rootDir)
    {
        $this->rootDir = $rootDir.'/../';
        $this->contributorRepository = $contributorRepository;
        $this->contributionRepository = $contributionRepository;
    }

    public function getData($projectId)
    {
        //$contents = file($this->rootDir.sprintf('trends/git-log-%d.txt', $projectId));

        $newEmails = [];

        $fileHandle = fopen($this->rootDir.sprintf('trends/git-log-%d.txt', $projectId), 'r');

        if($fileHandle === false) {

        }

        while (($lineParts = fgetcsv($fileHandle, 5000, ', ')) !== false) {


        // foreach ($contents as $line) {

            //$lineParts = explode(', ', $line);
            $name = trim($lineParts[0]);
            $email = trim($lineParts[1]);
            $dateTime = trim($lineParts[2]);
            $hash = trim($lineParts[3]);

//            $contributor = $this->createOrUpdateContributor($email, $name);
//            $this->contributorRepository->store($contributor);
//
//            $contribution = $this->createOrUpdateContribution($contributor, $projectId, $dateTime, $hash);
//            $this->contributionRepository->store($contribution);

            print '.';
        }


    }

    /**
     * @param $email
     * @param $name
     * @return Contributor
     */
    protected function createOrUpdateContributor($email, $name)
    {
        /** @var Contributor $contributor */
        $contributor = $this->contributorRepository->findByEmail($email);

        if (null !== $contributor) {
            if ($contributor->getName() !== $name &&
                !in_array($name, $contributor->getGitNames())
            ) {
                $contributor->setGitNames(array_filter(array_merge($contributor->getGitNames(), [$name])));
                $contributor->setUpdatedAt(new \DateTime());
            }

        } else {

            $contributor = new Contributor();
            $contributor
                ->setEmail($email)
                ->setName($name)
                ->setGitEmails([''])
                ->setGitNames([''])
                ->setCountries([''])
                ->setCommitCount(1)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

        }

        return $contributor;
    }

    /**
     * @param $contributor
     * @param $projectId
     * @param $dateTime
     * @param $hash
     *
     * @return Contribution
     */
    protected function createOrUpdateContribution(Contributor $contributor, $projectId, $dateTime, $hash)
    {
        $contribution = $this->contributionRepository->findOneBy(
            [
                'projectId' => $projectId,
                'contributorId' => $contributor->getId()
            ]
        );

        if (null === $contribution) {
            $contribution = new Contribution();
            $contribution
                ->setProjectId($projectId)
                ->setContributorId($contributor->getId())
                ->setCommitCount(0);
        }

        $contribution->setCommitCount($contribution->getCommitCount() + 1);

        if ('' === $contribution->getFirstCommitHash()) {
            $contribution
                ->setFirstCommitAt(new \DateTime($dateTime))
                ->setFirstCommitHash($hash);
        }

        return $contribution;
    }
}
