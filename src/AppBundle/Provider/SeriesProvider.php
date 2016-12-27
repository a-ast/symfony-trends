<?php


namespace AppBundle\Provider;

use AppBundle\Repository\DataProvider;

class SeriesProvider implements ProviderInterface
{
    private $dataProvider;

    /**
     * Constructor.
     *
     * @param DataProvider $dataProvider
     */
    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getSeries(array $options)
    {
        $seriesCollection = [];

        foreach ($options as $seriesConfig) {
            $data = $this->dataProvider->getData($seriesConfig['data_source'], $seriesConfig['criteria']);

            $seriesData = [];

            foreach ($data as $dataItem) {
                $seriesItem = [];

                foreach ($seriesConfig['dimensions'] as $key => $dimension) {
                    $seriesItem[$key] = $dataItem[$dimension];
                }

                $seriesData[] = $seriesItem;
            }

            $series = ['data' => $seriesData];

            if (isset($seriesConfig['title'])) {
                $series['name'] = $seriesConfig['title'];
            }

            if (isset($seriesConfig['color'])) {
                $series['color'] = $seriesConfig['color'];
            }

            $seriesCollection[] = $series;
        }

        return $seriesCollection;
    }
}
