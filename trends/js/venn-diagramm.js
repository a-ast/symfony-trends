function drawVennContributors(containerSelector, dataPath) {

    var chart = d3.select(containerSelector);

    var vennChart = venn.VennDiagram()
        .width(parseInt(chart.style("width")))
        .height(parseInt(chart.style("height")));


    d3.json(dataPath, function (error, data) {
        if (error) throw error;

        chart.datum(data).call(vennChart);

        //var c10 = d3.schemeCategory10();

        d3.selectAll(containerSelector + " .venn-circle path")
            .style("fill", function(d) { return d.color; });

        // Add transition on mouse over
        d3.selectAll(containerSelector + " .venn-circle")
            .on("mouseover", function(d, i) {
                var node = d3.select(this).transition();
                node.select("path").style("fill-opacity", d.sets.length == 1 ? .4 : .1);
            })
            .on("mouseout", function(d, i) {
                var node = d3.select(this).transition();
                node.select("path").style("fill-opacity", d.sets.length == 1 ? .25 : .0);

            });

    });

}
