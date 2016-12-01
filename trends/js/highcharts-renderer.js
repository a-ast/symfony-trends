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
            verticalAlign: "top"
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
                    format: "{point.name}<br\/>{point.y}"
                }
            }
        }
    });

}

HighchartsRenderer.prototype.columnChart = function(container, data) {

    var categories = [];
    data.series[0].data.forEach(function(value){
        categories.push(value[0]);
    });

    Highcharts.chart(container, {
        chart: {
            type: 'column'
        },
        title: {
            enabled: false
        },
        xAxis: {
            categories: categories
        },
        credits: {
            enabled: false
        },
        series: data.series
    });
}

var Renderer = new HighchartsRenderer();
