<?php


namespace AppBundle\Aggregator\Helper;

use GuzzleHttp\ClientInterface;

class GeolocationApiClient
{
    const BASE_URI = 'https://maps.googleapis.com/maps/api/geocode/json';
    
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $address
     *
     * @return string
     */
    public function findCountry($address)
    {
        $response = $this->httpClient->request('GET', self::BASE_URI, [
            'address' => $address,
            'http_errors' => false,
        ]);

        $data = json_decode($response->getBody(), true);

        if(0 === count($data['results'])) {
            // @todo    
        }
        
        $firstResult = array_pop($data['results']);

        $country = '';

        foreach ($firstResult['address_components'] as $location) {
            if(in_array('country', $location['types'])) {
                $country = $location['long_name'];
            }
        }

        return $country;
    }
}
