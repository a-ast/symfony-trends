<?php

namespace Aa\ATrends\Provider;

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
     * @inheritdoc
     */
    public function getSeries(array $options)
    {
        $seriesCollection = [];

        foreach ($options as $seriesConfig) {

            $limit = isset($seriesConfig['limit']) ? $seriesConfig['limit'] : null;

            $data = $this->dataProvider->getData($seriesConfig['data_source'], $seriesConfig['criteria'], $limit);

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
