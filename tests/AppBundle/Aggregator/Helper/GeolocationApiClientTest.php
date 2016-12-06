<?php

namespace Tests\AppBundle\Aggregator\Helper;

use AppBundle\Aggregator\Helper\GeolocationApiClient;
use PHPUnit_Framework_TestCase;
use Tests\AppBundle\Traits\HttpClientAwareTrait;

class GeolocationApiClientTest extends PHPUnit_Framework_TestCase
{
    use HttpClientAwareTrait;

    public function testGetCountry()
    {
        $json = file_get_contents(__DIR__.'/../fixtures/geo-locations/shire.json');

        $httpClient = $this->getHttpClient([[200, [], $json]]);

        $geolocationApiClient = new GeolocationApiClient($httpClient);
        $country = $geolocationApiClient->findCountry('Westfarthing, Shire, Middle-earth');

        $this->assertEquals('Middle-earth', $country);
    }
}
