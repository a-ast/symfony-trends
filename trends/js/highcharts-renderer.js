function HighchartsRenderer () {


}

HighchartsRenderer.prototype.columnChart = function(container, data) {

    var series = [];
    data.series.forEach(function(value){
        series.push({data: value});
    });

    var categories = [];
    data.series[0].forEach(function(value){
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
        series: series
    });
}

var HighchartsRenderer = new HighchartsRenderer();
