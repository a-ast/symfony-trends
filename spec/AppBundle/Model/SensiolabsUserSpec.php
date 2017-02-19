<?php

namespace spec\AppBundle\Model;

use AppBundle\Model\SensiolabsUser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin SensiolabsUser
 */
class SensiolabsUserSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('frodo', 'Frodo Baggins', 'Bag End', 'Shire',
            'github', 'facebook', 'twitter', 'linkedIn',
            'website', 'blog', 'blogFeed');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SensiolabsUser::class);
    }

    public function it_returns_property_values()
    {
        $this->getLogin()->shouldReturn('frodo');
        $this->getName()->shouldReturn('Frodo Baggins');
        $this->getCity()->shouldReturn('Bag End');
        $this->getCountry()->shouldReturn('Shire');

        $this->getGithubUrl()->shouldReturn('github');
        $this->getFacebookUrl()->shouldReturn('facebook');
        $this->getTwitterUrl()->shouldReturn('twitter');
        $this->getLinkedInUrl()->shouldReturn('linkedIn');
        $this->getWebsiteUrl()->shouldReturn('website');
        $this->getBlogUrl()->shouldReturn('blog');
        $this->getBlogFeedUrl()->shouldReturn('blogFeed');
    }
}
