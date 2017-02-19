<?php

namespace spec\AppBundle\Api\Sensiolabs;

use AppBundle\Api\Sensiolabs\SensiolabsUserDataExtractor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @mixin SensiolabsUserDataExtractor
 */
class SensiolabsUserDataExtractorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SensiolabsUserDataExtractor::class);
    }

    public function it_extracts_from_partially_filled_profile_page()
    {
        $crawler = new Crawler($this->getPartialProfile(), 'http');

        $this
            ->extract($crawler)
            ->shouldReturn([
                'name' => 'Frodo Baggins',
                'city' => 'Bag End',
                'country' => 'Middle Earth',
                'github' => 'http://valinor-github/frodo',
            ]);
    }

    public function it_extracts_from_fully_filled_profile_page()
    {
        $crawler = new Crawler($this->getFullProfile(), 'http');

        $this
            ->extract($crawler)
            ->shouldReturn([
                'name' => 'Gandalf',
                'city' => 'Valinor',
                'country' => 'Middle Earth',
                'github' => 'https://valinor-github/gandalf',
                'facebook' => 'http://valinor-facebook/gandalf',
                'twitter' => 'http://valinor-twitter/gandalf',
                'linkedin' => 'http://valinor-linkedin/gandalf',
                
                'website' => 'http://gandalf.magic',
                'blog' => 'http://gandalf.magic/blog',
                'blog_feed' => 'http://rss.gandalf.magic/blog',
            ]);
    }

    private function getPartialProfile()
    {
        return <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>

    <div id="profile-avatar-container">
        <div id="profile-username-container">
            <h1 itemprop="name">Frodo Baggins</h1>
            <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <span itemprop="addressLocality">Bag End</span>, 
                <span itemprop="addressCountry">Middle Earth</span>
            </p>
        </div>
    </div>

    <section>
        <h2 class="separator">About</h2>
        <p itemprop="description">Ring destroyer</p>
        <ul class="tags unstyled">
            <li>Links: </li>
            <li class="tag"><a itemprop="url" href="http://valinor-github/frodo">Github</a></li>
        </ul>
    </section>

</body>
</html>
EOD;
    }

    private function getFullProfile()
    {
        return <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>

    <div id="profile-avatar-container">
        <div id="profile-username-container">
            <h1 itemprop="name">Gandalf</h1>
            <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <span itemprop="addressLocality">Valinor</span>,
                <span itemprop="addressCountry">Middle Earth</span>
            </p>
        </div>
    </div>

    <section>
        <h2 class="separator">About</h2>
        <p itemprop="description">Ring hand-over</p>
        <ul class="tags unstyled">
            <li>Links: </li>
            <li class="tag"><a itemprop="url" href="http://valinor-facebook/gandalf">Facebook</a></li>
            <li class="tag"><a itemprop="url" href="https://valinor-github/gandalf">Github</a></li>
            
            <li class="tag"><a itemprop="url" href="http://valinor-twitter/gandalf">Twitter</a></li>
            <li class="tag"><a itemprop="url" href="http://valinor-linkedin/gandalf">LinkedIn</a></li>
            
            <li class="tag"><a itemprop="url" href="http://gandalf.magic">Website</a></li>
            <li class="tag"><a itemprop="url" href="http://gandalf.magic/blog">Blog</a></li>
            <li class="tag"><a itemprop="url" href="http://rss.gandalf.magic/blog">Blog feed</a></li>
        </ul>
    </section>

</body>
</html>
EOD;
    }

}
