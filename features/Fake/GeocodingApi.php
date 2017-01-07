<?php


namespace features\Fake;

use Geocoder\Provider\Provider;

class GeocodingApi implements Provider
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
        return $this->data[$value];
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

    /**
     * @inheritdoc
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }
}
