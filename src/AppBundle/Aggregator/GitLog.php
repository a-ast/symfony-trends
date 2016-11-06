<?php


namespace AppBundle\Aggregator;

use AppBundle\Entity\ContributionLog;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\ProgressInterface;
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

            ->setDefined('update_log')
            ->setAllowedTypes('update_log', 'bool')
            ->setDefault('update_log', false)

            ->setDefined('since_datetime')
            ->setAllowedTypes('since_datetime', 'string')
            ->setDefault('since_datetime', '');

        return $resolver->resolve($options);
    }

    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $options = $this->resolveOptions($options);

        $projectId = $options['project_id'];

        $contents = file($this->gitLogDir.sprintf('git-log-%d.txt', $projectId));


//        $fileHandle = fopen($this->gitLogDir.sprintf('git-log-%d.txt', $projectId), 'r');
//
//        if ($fileHandle === false) {
//            // @todo: throw exception
//        }
//
//        while (($lineParts = fgetcsv($fileHandle, 5000, ',')) !== false) {

        $i = 0;
        $batchSize = 500;

        foreach ($contents as $line) {

            $lineParts = explode(',', $line);

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

                $contributionLogEntry = $this->createContributionLog($contributor, $projectId, $dateTime, $hash);

                $this->repositoryFacade->persist($contributionLogEntry);
            }

            print '.';
            $i++;


            if (($options['update_contributors'] ||
                $options['update_log']) && ($i % $batchSize) === 0) {
                $this->repositoryFacade->flush();
            }
        }

        if ($options['update_contributors'] ||
            $options['update_log']) {
            $this->repositoryFacade->flush();
        }

        return [];
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
            ->setSensiolabsPageError(0)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        return $contributor;
    }

    /**
     * @param Contributor $contributor
     * @param int $projectId
     * @param DateTime $dateTime
     * @param string $hash
     *
     * @return ContributionLog
     */
    protected function createContributionLog(Contributor $contributor, $projectId, DateTime $dateTime, $hash)
    {
        $contributionLogEntry = new ContributionLog();
        $contributionLogEntry
            ->setProjectId($projectId)
            ->setContributorId($contributor->getId())
            ->setCommitedAt($dateTime)
            ->setCommitHash($hash);

        return $contributionLogEntry;
    }
}
