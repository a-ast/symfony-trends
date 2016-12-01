function HighchartsRenderer () {


}

HighchartsRenderer.prototype.columnChart = function(container, data) {

    var series = [];
    data.series.forEach(function(value){
        series.push({data: value});
    });

    Highcharts.chart(container, {
        chart: {
            type: 'column'
        },
        title: {
            enabled: false
        },
        xAxis: {

        },
        credits: {
            enabled: false
        },
        series: series
    });
}
