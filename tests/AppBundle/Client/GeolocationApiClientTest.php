<?php

namespace Tests\AppBundle\Aggregator\Helper;

use AppBundle\Client\GeolocationApiClient;
use PHPUnit_Framework_TestCase;
use Tests\AppBundle\Traits\HttpClientAwareTrait;

class GeolocationApiClientTest extends PHPUnit_Framework_TestCase
{
    use HttpClientAwareTrait;

    public function testGetCountry()
    {
        $json = file_get_contents(__DIR__.'/fixtures/geo-location.json');

        $httpClient = $this->getHttpClient([[200, [], $json]]);

        $geolocationApiClient = new GeolocationApiClient($httpClient, 'api-key');
        $result = $geolocationApiClient->findCountry('Westfarthing, Shire, Middle-earth');

        $expected = [
            'country' => 'Middle-earth',
            'exact_match' => false,
        ];

        $this->assertEquals($expected, $result);
    }
}
