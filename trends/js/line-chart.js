function drawLineChart(containerSelector, dataPath, xAxisLabel) {

    var chartContainer = d3.select(containerSelector),

        chart = chartContainer.append("svg")
            .attr("width", parseInt(chartContainer.style("width")))
            .attr("height", parseInt(chartContainer.style("height")));

    var margin = {top: 20, right: 20, bottom: 40, left: 40},
        width = +chart.attr("width") - margin.left - margin.right,
        height = +chart.attr("height") - margin.top - margin.bottom;

    var parseDate = d3.timeParse("%Y-%m-%d");

    var x = d3.scaleTime()
        .rangeRound([0, width]);

    var y = d3.scaleLinear()
        .rangeRound([height, 0]);

    var line = d3.line()
        .curve(d3.curveBasis)
        .x(function(d) { return x(d.date); })
        .y(function(d) { return y(d.value); });

    d3.json(dataPath, function(error, data) {
        if (error) throw error;

        data.forEach(function (d) {
            d.date = parseDate(d.date);
            d.value = +d.value;
        });

        x.domain(d3.extent(data, function(d) { return d.date; }));
        y.domain(d3.extent(data, function(d) { return d.value; }));


        // Nest the entries by symbol
        var dataNest = d3.nest()
            .key(function(d) {return d.seriesId;})
            .entries(data);

        // Loop through each symbol / key
        dataNest.forEach(function(d) {

            chart.append("path")
                .attr("class", "line")
                .attr("d", line(d.values));

        });

        chart.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x));
        //
        // chart.append("g")
        //     .attr("class", "axis axis--y")
        //     .call(d3.axisLeft(y))
        //     .append("text")
        //     .attr("fill", "#000")
        //     .attr("transform", "rotate(-90)")
        //     .attr("y", 6)
        //     .attr("dy", "0.71em")
        //     .style("text-anchor", "end")
        //     .text("Price ($)");

        // chart.append("path")
        //     .datum(data)
        //     .attr("class", "line")
        //     .attr("d", line);


    });

}
