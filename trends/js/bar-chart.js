function drawBarChart(containerSelector, dataPath, xAxisLabel) {

    var chartContainer = d3.select(containerSelector),

        chart = chartContainer.append("svg")
            .attr("width", parseInt(chartContainer.style("width")))
            .attr("height", parseInt(chartContainer.style("height")));

    var margin = {top: 20, right: 20, bottom: 40, left: 40},
        width = +chart.attr("width") - margin.left - margin.right,
        height = +chart.attr("height") - margin.top - margin.bottom;

    var x = d3.scaleBand().rangeRound([0, width]).padding(0.1),
        y = d3.scaleLinear().rangeRound([height, 0]);


    d3.json(dataPath, function(error, data) {
        if (error) throw error;

        var xScale = x.domain(data.map(function(d) { return d.label; }));
        y.domain([0, d3.max(data, function(d) { return d.value; })]);

        var bar = chart.selectAll("g")
            .data(data)
            .enter().append("g")
            .attr("transform", function(d) { return "translate(" + x(d.label) + ",0)"; });

        bar.append("rect")
            .attr("y", function(d) { return y(d.value); })
            .attr("height", function(d) { return height - y(d.value); })
            .attr("width", x.bandwidth());

        bar.append("text")
            .attr("class", "bar-label")
            .attr("x", x.bandwidth() / 2)
            .attr("y", function(d) { return y(d.value) + 10; })
            .attr("dy", ".75em")
            .text(function(d) { return d.value; });

        //d3.svg.axis().tickSize(0);

        var xAxis = d3.axisBottom().scale(xScale);

        chart.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis)
        ;

        chart.append("text")
            .attr("class", "x-axis-label")
            .attr("transform",
                "translate(" + (width/2) + " ," +
                (height + margin.top + 25) + ")")
            .style("text-anchor", "middle")
            .text(xAxisLabel);

    });

}
