<?php

namespace AppBundle\Command;

use AppBundle\CrawlerOrchestrator;
use AppBundle\Repository\ContributorRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckNewContributorsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:check-new-contributors')
            ->setDescription('Check new contributors')
            ->addArgument('project-id')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('project-id');

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir').'/../';

        /** @var ContributorRepository $repo */
        $repo = $this->getContainer()->get('repository.contributor');


        $fileHandle = fopen($rootDir.sprintf('trends/git-log-%d.txt', $projectId), 'r');

        if($fileHandle === false) {

        }

        $processedEmails = [];

        $newEmailsCount = 0;
        $existingEmailsCount = 0;

        while (($lineParts = fgetcsv($fileHandle, 5000, ',')) !== false) {

            $name = trim($lineParts[0]);
            $email = trim($lineParts[1]);

            if(isset($processedEmails[$email])) {
                continue;
            }

            $processedEmails[$email] = true;

            $contributor = $repo->findByEmail($email);

            if(null !== $contributor) {
                $existingEmailsCount++;
                continue;
            }

            $newEmailsCount++;

            $contributorsByName = $repo->findByName($name);

            print $newEmailsCount.'. '.$email .' | '.$name.PHP_EOL;

            if(count($contributorsByName) > 0) {


                print '   Maybe one of them?:'.PHP_EOL;
                foreach ($contributorsByName as $contributorByName) {
                    print '   - '.implode(', ', $contributorByName->getAllNames()).PHP_EOL;
                }
            }
        }

        $output->writeln(sprintf('<info>Total new: %d</info>', $newEmailsCount));
        $output->writeln(sprintf('<info>Existing : %d</info>', $existingEmailsCount));

    }
}
