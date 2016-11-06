<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class BackupSqliteFileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:backup-sqlite-file')
            ->setDescription('Backup sqlite file')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getContainer()->getParameter('database_path');
        $now = new \DateTime();
        $newPath = $path.'.'.$now->format(DATE_W3C);

        $fs = new Filesystem();
        $fs->copy($path, $newPath);

        $output->writeln(sprintf('<info>File copied to: %s</info>', $newPath));
    }
}
