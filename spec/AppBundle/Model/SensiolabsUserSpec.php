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
    public function it_is_initializable()
    {
        $this->shouldHaveType(SensiolabsUser::class);
    }

    public function it_returns_property_values()
    {
        $this->beConstructedThrough('createFromArray', [$this->getArrayData()]);

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

    private function getArrayData()
    {
        return [
            'name' => 'Frodo Baggins',
            'city' => 'Bag End',
            'country' => 'Shire',
            'githubUrl' => 'github',
            'facebookUrl' => 'facebook',
            'twitterUrl' => 'twitter',
            'linkedInUrl' => 'linkedIn',
            'websiteUrl' => 'website',
            'blogUrl' => 'blog',
            'blogFeedUrl' => 'blogFeed',
        ];
    }
}
