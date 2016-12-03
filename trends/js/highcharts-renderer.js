function HighchartsRenderer () {

    Highcharts.setOptions({
        title: {
            text: null
        },
        credits: {
            enabled: false
        },
        legend: {
            enabled: true,
            verticalAlign: 'top'
        },
        xAxis: {
            tickLength: 0
        },
        yAxis: {
            endOnTick: false,
            title: {
                text: null
            }
        },
        plotOptions: {
            area: {
                marker: {
                    enabled: false
                }
            },
            pie: {
                dataLabels: {
                    format: '{point.name}<br\/>{point.y}'
                }
            }
        }
    });
}

HighchartsRenderer.prototype.columnChart = function(container, data) {

    var categories = [];

    data.series.forEach(function(series) {
        series.data.forEach(function(item) {
            categories.push(item['name']);
            item['y'] = item['value'];
            delete item['name'];
            delete item['value'];
        })
    });

    Highcharts.chart(container, {
        chart: {
            type: 'column'
        },
        xAxis: {
            categories: categories
        },
        series: data.series
    });
};

HighchartsRenderer.prototype.stairAreaChart = function(container, data) {

    var categories = [];

    data.series.forEach(function(series) {

        series.data.forEach(function(item) {
            if (categories.indexOf(item['name']) == -1) {
                categories.push(item['name']);
            }
            item['y'] = item['value'];
            delete item['value'];
        });

        if (categories.indexOf('') == -1) {
            categories.push('');
        }
        series.data.push({name: '', y: series.data[series.data.length-1].y});
    });

    Highcharts.chart(container, {
        chart: {
            type: 'area'
        },
        xAxis: {
            categories: categories
        },
        plotOptions: {
            area: {
                step: 'left'
            }
        },
        series: data.series
    });
};

HighchartsRenderer.prototype.stairAreaDateTimeChart = function(container, data) {

    data.series.forEach(function(series) {

        series.data.forEach(function(item) {

            item['x'] = Date.parse(item['date']);
            item['y'] = item['value'];
            delete item['date'];
            delete item['value'];
        });

        series.data.unshift({x: series.data[0].x, y: series.data[0].y});
    });

    Highcharts.chart(container, {
        chart: {
            type: 'area'
        },
        xAxis: {
            type: 'datetime'
        },
        plotOptions: {
            area: {
                step: 'left'
            }
        },
        series: data.series
    });
};


HighchartsRenderer.prototype.pieChart = function(container, data) {

    // replace element with y, name, color
    data.series.forEach(function(series) {
        series.data.forEach(function(item) {
            item['y'] = item['value'];
            delete item['value'];
        })
    });

    Highcharts.chart(container, {
        chart: {
            type: 'pie'
        },
        series: data.series
    });
};

HighchartsRenderer.prototype.mapChart = function(container, data) {

    Highcharts.mapChart(container, {
        chart: {
            type: 'map',
            map: 'custom/world'
        },
        legend: {
            enabled: false
        },
        colorAxis: {
            min: 1,
            type: 'logarithmic',
            minColor: '#f0faff',
            maxColor: '#045d87'
        },
        plotOptions: {
            map: {
                nullColor: '#fff',
                joinBy: ['iso-a2', 'iso']
            }
        },
        series: data.series
    });
};

var Renderer = new HighchartsRenderer();
