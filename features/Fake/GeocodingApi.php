<?php


namespace features\Fake;

use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\Country;

class GeocodingApi implements Geocoder
{
    /**
     * @var array
     */
    private $data = [];

    public function addData($dataType, array $data)
    {
        $this->data[$data['location']] = $data['country'];
    }

    /**
     * @inheritdoc
     */
    public function geocode($value)
    {
        $countryName = $this->data[$value];

        $address = new Address(null, null, null, null, null, null, null, null,
            new Country($countryName, 'SR'));

        return new \ArrayIterator([$address]);
    }

    /**
     * @inheritdoc
     */
    public function reverse($latitude, $longitude)
    {
        // TODO: Implement reverse() method.
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        // TODO: Implement getLimit() method.
    }

    /**
     * @inheritdoc
     */
    public function limit($limit)
    {
        // TODO: Implement limit() method.
    }
}
