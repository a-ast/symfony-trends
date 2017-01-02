<?php

use AppBundle\Entity\Project;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Defines application features from the specific context.
 */
class AggregatorFeatureContext implements Context
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var PurgerInterface
     */
    private $purger;

    /**
     * Initializes context.
     *
     * @param ObjectManager $em
     * @param PurgerInterface $purger
     */
    public function __construct(ObjectManager $em, PurgerInterface $purger)
    {
        $this->em = $em;
        $this->purger = $purger;
    }

    /**
     * @Given I have existing projects:
     *
     * @param TableNode $projects
     */
    public function existingProjects(TableNode $projects)
    {
        $this->purger->purge();

        $query = $this->em->createNativeQuery('ALTER SEQUENCE project_id_seq RESTART;', new \Doctrine\ORM\Query\ResultSetMapping());
        $query->execute();

        foreach ($projects as $projectData) {

            $project = new Project();
            $project
                ->setName($projectData['name'])
                ->setLabel($projectData['label'])
                ->setGithubPath($projectData['path'])
                ->setColor($projectData['color']);

            $this->em->persist($project);
        }

        $this->em->flush();
    }

    /**
     * @Given API commits:
     */
    public function givenApiCommits(TableNode $commits)
    {
//        foreach ($tableAuthors as $authorRow) {
//            $author = new Author();
//            $author->setName($authorRow['name']);
//            $this->em->persist($author);
//        }
//        $this->em->flush();
    }

}

