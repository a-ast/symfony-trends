<?php


namespace AppBundle\Provider;

class SeriesProvider
{
    private $dataProvider;

    /**
     * Constructor.
     */
    public function __construct($dataProvider)
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

            $seriesCollection[] = $series;
        }

        return $seriesCollection;
    }
}
