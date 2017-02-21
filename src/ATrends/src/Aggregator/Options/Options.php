<?php


namespace Aa\ATrends\Aggregator\Options;

class Options implements OptionsInterface
{

    /**
     * @var string
     */
    private $updateSince;

    /**
     * Constructor.
     *
     * @param string $updateSince
     */
    public function __construct($updateSince)
    {
        $this->updateSince = $updateSince;
    }

    /**
     * @return string
     */
    public function getUpdateSince()
    {
        return $this->updateSince;
    }
}
