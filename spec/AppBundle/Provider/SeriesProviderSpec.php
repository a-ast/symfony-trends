<?php

namespace spec\AppBundle\Provider;

use AppBundle\Provider\SeriesProvider;
use AppBundle\Repository\DataProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin SeriesProvider
 */
class SeriesProviderSpec extends ObjectBehavior
{
    function it_is_initializable(DataProvider $dataProvider)
    {
        $this->beConstructedWith($dataProvider);

        $this->shouldHaveType(SeriesProvider::class);
    }

    function it_returns_series(DataProvider $dataProvider)
    {
        $this->beConstructedWith($dataProvider);

        $dataProvider
            ->getData(Argument::any(), Argument::any(), null)
            ->willReturn([['id' => 1, 'name' => 'Gandalf', 'age' => 2019], ['id' => 1, 'name' => 'Frodo', 'age' => 33]]);

        $options = [
            [
                'data_source' => 'data_view',
                'criteria' => [],
                'dimensions' => ['name', 'age'],
            ]
        ];

        $this->getSeries($options)->shouldReturn([['data' => [['Gandalf', 2019], ['Frodo', 33]]]]);
    }
}
