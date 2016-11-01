<?php


namespace AppBundle\Aggregator;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\ContributionHistory;
use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepositoryFacade;
use DateTime;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Git log aggregator
 */
class GitLog implements AggregatorInterface
{
    /**
     * @var string
     */
    private $gitLogDir;

    /**
     * @var ContributorRepositoryFacade
     */
    private $repositoryFacade;

    /**
     * Constructor.
     *
     * @param ContributorRepositoryFacade $repositoryFacade
     * @param string $gitLogDir
     */
    public function __construct(ContributorRepositoryFacade $repositoryFacade, $gitLogDir)
    {
        $this->gitLogDir = $gitLogDir;
        $this->repositoryFacade = $repositoryFacade;
    }

    protected function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();

        // All data updates are disabled by default
        $resolver
            ->setRequired(['project_id'])
            ->setAllowedTypes('project_id', 'int')
            ->setDefined('update_contributors')
            ->setAllowedTypes('update_contributors', 'bool')
            ->setDefault('update_contributors', false)
            ->setDefined('update_contributions')
            ->setAllowedTypes('update_contributions', 'bool')
            ->setDefault('update_contributions', false)
            ->setDefined('update_log')
            ->setAllowedTypes('update_log', 'bool')
            ->setDefault('update_log', false)
            ->setDefined('since_datetime')
            ->setAllowedTypes('since_datetime', 'string')
            ->setDefault('since_datetime', '');

        return $resolver->resolve($options);
    }

    public function aggregate(array $options = [])
    {

        $options = $this->resolveOptions($options);

        //$contents = file($this->rootDir.sprintf('trends/git-log-%d.txt', $projectId));

        $projectId = $options['project_id'];

        $fileHandle = fopen($this->gitLogDir.sprintf('git-log-%d.txt', $projectId), 'r');

        if ($fileHandle === false) {
            // @todo: throw exception
        }

        while (($lineParts = fgetcsv($fileHandle, 5000, ',')) !== false) {

            $name = trim($lineParts[0]);
            $email = trim($lineParts[1]);
            $dateTime = new DateTime(trim($lineParts[2]));
            $hash = trim($lineParts[3]);

            // Skip if the date less than the given one
            if ('' !== $options['since_datetime']) {
                $sinceDateTime = new DateTime($options['since_datetime']);

                if ($dateTime <= $sinceDateTime) {
                    continue;
                }
            }

            $contributor = $this->repositoryFacade->findContributorByEmail($email);

            if ($options['update_contributors']) {
                if (null === $contributor) {
                    $contributor = $this->createContributor($email, $name);
                    $this->repositoryFacade->persist($contributor);
                }

                $contributor->addGitName($name);
            }


            if ($options['update_log']) {

                if (null === $contributor) {
                    throw new RuntimeException(sprintf(
                        'Contributor [%s] does not exist but contribution log entry must be created', $name));
                }

                $contributionLogEntry = new ContributionHistory();
                $contributionLogEntry
                    ->setProjectId($projectId)
                    ->setContributorId($contributor->getId())
                    ->setCommitedAt(new DateTime($dateTime))
                    ->setCommitHash($hash);

                $this->repositoryFacade->persist($contributionLogEntry);
            }

//
//            $contribution = $this->createOrUpdateContribution($contributor, $projectId, $dateTime, $hash);
//            $this->contributionRepository->store($contribution);

            print '.';

            if ($options['update_contributors'] || $options['update_contributions'] || $options['update_log']) {
                $this->repositoryFacade->flush();
            }
        }


    }

    /**
     * @param $email
     * @param $name
     *
     * @return Contributor
     */
    protected function createContributor($email, $name)
    {
        $contributor = new Contributor();
        $contributor
            ->setEmail($email)
            ->setName($name)
            ->setGitEmails([''])
            ->setGitNames([''])
            ->setCountries([''])
            ->setCommitCount(1)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

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
        $contribution = $this->repositoryFacade->findOneBy([
                'projectId' => $projectId,
                'contributorId' => $contributor->getId(),
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
                ->setFirstCommitAt(new DateTime($dateTime))
                ->setFirstCommitHash($hash);
        }

        return $contribution;
    }
}
