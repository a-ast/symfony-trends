<?php

namespace AppBundle\Command;

use AppBundle\Entity\Contributor;
use AppBundle\Repository\ContributorRepository;
use Aa\ATrends\Util\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FixContributorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('trends:data:fix-contributor')
            ->setDescription('Fix contributors data')
            ->addArgument('id', null, 'Contributor id')
            ->addOption('take-longest', 'l')
            ->addOption('merge-emails', 'm', InputOption::VALUE_REQUIRED)
            ->addOption('take-this', 't', InputOption::VALUE_REQUIRED)
            ->addOption('original-data', 'o', InputOption::VALUE_REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContributorRepository $repository */
        $repository = $this->getContainer()->get('repository.contributor');

        $id = $input->getArgument('id');

        /** @var Contributor $contributor */
        $contributor = $repository->find($id);

        $names = ArrayUtils::trimMerge($contributor->getName(), $contributor->getGitNames());

        if(true === $input->getOption('take-longest')) {

            usort($names, function ($a, $b)
            {
                if (strlen($a) == strlen($b)) {
                    return 0;
                }
                return (strlen($a) > strlen($b)) ? -1 : 1;
            });

            $contributor->setName($names[0]);

            array_shift($names);

            $contributor->setGitNames($names);
            $repository->store($contributor);


        } elseif (null !== $input->getOption('merge-emails')) {

            $anotherContributorId = $input->getOption('merge-emails');
            /** @var Contributor $anotherContributor */
            $anotherContributor = $repository->find($anotherContributorId);
            $newEmails = array_filter(array_merge($contributor->getGitEmails(),
                [$anotherContributor->getEmail()], $anotherContributor->getGitEmails()));

            $contributor->setGitEmails($newEmails);
            $repository->store($contributor);
            $repository->remove($anotherContributor);
        } elseif (null !== $input->getOption('take-this')) {

            $newName = $input->getOption('take-this');

            if(!$newName) {
                throw new \Exception('Name must be not empty.');
            }

            $contributor->setName($newName);
            $contributor->setGitNames($names);
            $repository->store($contributor);
        }

    }


}
