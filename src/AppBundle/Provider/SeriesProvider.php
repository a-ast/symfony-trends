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

            $series = [];

            foreach ($data as $dataItem) {
                $seriesItem = [];

                foreach ($seriesConfig['values'] as $fieldKey => $fieldName) {
                    $seriesItem[$fieldKey] = $dataItem[$fieldName];
                }

                $series[] = $seriesItem;
            }

            $seriesCollection[] = [
                'name' => $seriesConfig['title'],
                'color' => $seriesConfig['color'],
                'data' => $series
            ];
        }

        return $seriesCollection;
    }
}
