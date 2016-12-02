<?php


namespace AppBundle\Provider;

use AppBundle\Repository\DataProvider;

class SeriesProvider
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

    public function getSeries(array $options)
    {
        $seriesCollection = [];

        foreach ($options as $seriesConfig) {
            $data = $this->dataProvider->getData($seriesConfig['data_source'], $seriesConfig['filters']);

            $seriesData = [];

            foreach ($data as $dataItem) {
                $seriesItem = [];

                foreach ($seriesConfig['values'] as $fieldKey => $fieldName) {
                    $seriesItem[$fieldKey] = $dataItem[$fieldName];
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
