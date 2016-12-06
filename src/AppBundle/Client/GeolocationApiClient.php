<?php


namespace AppBundle\Client;

use GuzzleHttp\ClientInterface;
use Exception;

class GeolocationApiClient
{
    const BASE_URI = 'http://maps.googleapis.com/maps/api/geocode/json';
    const BASE_HTTPS_URI = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param string $apiKey
     */
    public function __construct(ClientInterface $httpClient, $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $address
     *
     * @return string
     */
    public function findCountry($address)
    {
        $query = [
            'address' => $address,
            'key' => $this->apiKey,
        ];

//        $response = $this->httpClient->request('GET', self::BASE_URI, [
//            'query' => $query,
//            'http_errors' => false,
//        ]);
//
//        $data = json_decode($response->getBody(), true);

        $queryString = http_build_query($query);
        $url = self::BASE_HTTPS_URI.'?'.$queryString;

        $contents = file_get_contents($url);
        $data = json_decode($contents, true);

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
        print '['.$country.']';

        return [
            'country' => $country,
            'exact_match' => !$partialMatch,
        ];
    }
}
