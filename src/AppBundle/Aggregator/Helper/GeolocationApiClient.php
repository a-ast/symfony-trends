<?php


namespace AppBundle\Aggregator\Helper;

use GuzzleHttp\ClientInterface;
use Exception;

class GeolocationApiClient
{
    const BASE_URI = 'http://maps.googleapis.com/maps/api/geocode/json';
    
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
            'query' => ['address' => $address],
            'http_errors' => false,
        ]);

        $data = json_decode($response->getBody(), true);

        if(0 === count($data['results'])) {
            // @todo    
        }
        
        $firstResult = array_pop($data['results']);

        $partialMatch = isset($firstResult['partial_match']) && true === $firstResult['partial_match'];

        $country = '';

        if (isset($firstResult['address_components'])) {
            foreach ($firstResult['address_components'] as $location) {
                if (in_array('country', $location['types'])) {
                    $country = $location['long_name'];
                }
            }
        }

        return [
            'country' => $country,
            'exact_match' => !$partialMatch,
        ];
    }
}
