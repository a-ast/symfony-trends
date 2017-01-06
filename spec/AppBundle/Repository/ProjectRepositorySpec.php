<?php

namespace spec\AppBundle\Repository;

use AppBundle\Entity\Project;
use AppBundle\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ProjectRepository
 */
class ProjectRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProjectRepository::class);
    }
}
