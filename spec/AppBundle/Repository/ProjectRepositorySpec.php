<?php

namespace spec\AppBundle\Repository;

use AppBundle\Entity\Project;
use AppBundle\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ProjectRepository
 */
class ProjectRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata, UnitOfWork $uow, EntityPersister $persister)
    {
        $uow->getEntityPersister(Argument::any())->willReturn($persister);
        $em->getUnitOfWork()->willReturn($uow);

        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProjectRepository::class);
    }

    function it_returns_projects_by_label(EntityPersister $persister, Project $project)
    {
        $persister->load(Argument::cetera())->willReturn($project);

        $projects = $this->findByLabel(['valinor']);
        $projects->shouldBeArray();

        $expectedProject = $projects[0];
        $expectedProject->shouldHaveType(Project::class);
    }

    function it_fails_if_project_with_given_label_does_not_exist(EntityPersister $persister, Project $project)
    {
        $persister->load(Argument::cetera())->willReturn(null);

        $this->shouldThrow('\InvalidArgumentException')->during('findByLabel', [['valinor']]);
    }
}
