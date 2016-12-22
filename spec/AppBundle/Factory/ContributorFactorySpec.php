<?php

namespace spec\AppBundle\Factory;

use AppBundle\Entity\Contributor;
use AppBundle\Factory\ContributorFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ContributorFactory
 */
class ContributorFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContributorFactory::class);
    }

    function it_creates_contributor_from_email()
    {
        $contributor = $this->createFromEmail('frodo@shire');
        $contributor->shouldReturnAnInstanceOf(Contributor::class);
        $contributor->getEmail()->shouldBe('frodo@shire');
    }

    function it_creates_contributor_from_array()
    {
        $contributor = $this->createFromArray(['email' => 'frodo@shire', 'name' => 'Frodo']);
        $contributor->shouldReturnAnInstanceOf(Contributor::class);
        $contributor->getEmail()->shouldBe('frodo@shire');
        $contributor->getName()->shouldBe('Frodo');
    }
}
