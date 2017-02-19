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

    public function it_extracts_user_with_partially_filled_profile()
    {
        $crawler = new Crawler($this->getPartialProfile(), 'http');

        $this
            ->extract($crawler)
            ->shouldReturn([
                'name' => 'Frodo Baggins',
                'city' => 'Bag End',
                'country' => 'Shire',
                'github_url' => 'http://valinor-github/frodo',
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
                <span itemprop="addressCountry">Shire</span>
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
}
