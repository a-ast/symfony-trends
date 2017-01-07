<?php


namespace features\Fake;

use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\Country;

class GeocoderApi implements Geocoder
{
    use FakeDataAware;

    /**
     * @inheritdoc
     */
    public function geocode($value)
    {
        $data = $this->findBy('location', 'location', $value);

        $countryName = $data['country'];

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
