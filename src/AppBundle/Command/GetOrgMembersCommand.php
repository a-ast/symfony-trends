<?php

namespace AppBundle\Command;

use Aa\ATrends\Api\Github\GithubApiInterface;
use DateTime;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class GetOrgMembersCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:org-members')
            ->setDescription('Fetch organization members')
           ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GithubApiInterface $api */
        $api = $this->getContainer()->get('api.github');

        $members = $api->getOrganizationMembers('inviqa');

        $output->writeln(sprintf('<info>Finished</info>'));
    }
}
