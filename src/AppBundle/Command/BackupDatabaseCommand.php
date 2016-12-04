<?php

namespace AppBundle\Command;

use DateTime;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:backup-satabase')
            ->setDescription('Backup database')
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path to store backups.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // How to backup postgres database
        // pg_dump --port 5432 --username "postgres" --no-password  --format custom --blobs --verbose --file "/path/to/file/file.backup"

        $configFilePath = $this->getContainer()->getParameter('kernel.cache_dir').'/trends/backup-path.data';

        $path = $input->getOption('path') ? $input->getOption('path') : $this->getBackupDirPath($configFilePath);

        $fs = new Filesystem();
        $fs->dumpFile($configFilePath, $path);

        /** @var Connection $dbName */
        $connection = $this->getContainer()->get('database_connection');
        $dbUserName = $connection->getUsername();
        $dbPort = $connection->getPort();
        $dbName = $connection->getDatabase();
        $now = new DateTime();
        $backupPath = $path.'/'.$now->format('Y-m-d-H-i').'.backup';

        $commandFormat = 'pg_dump --port %s --username "%s" --no-password --format custom --blobs --verbose --file "%s" "%s"';
        $command = sprintf($commandFormat, $dbPort, $dbUserName, $backupPath, $dbName);
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output->writeln(sprintf('<info>New backup created: %s</info>', $backupPath));
    }

    /**
     * @param string $configFilePath
     *
     * @return string
     */
    protected function getBackupDirPath($configFilePath)
    {
        $fs = new Filesystem();

        if ($fs->exists($configFilePath)) {
            return file_get_contents($configFilePath);
        }

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        return $rootDir.'/../var/backups';
    }
}
