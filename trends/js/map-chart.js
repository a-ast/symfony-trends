function drawMapChart(containerSelector, dataPath, xAxisLabel) {

    d3.json(dataPath, function(error, data) {
        if (error) throw error;

        // Datamaps expect data in format:
        // { "USA": { "fillColor": "#42a844", numberOfWhatever: 75},
        //   "FRA": { "fillColor": "#8dc386", numberOfWhatever: 43 } }
        var dataset = {};

        // We need to colorize every country based on "numberOfWhatever"
        // colors should be uniq for every value.
        // For this purpose we create palette(using min/max series-value)
        var onlyValues = data.map(function(obj){ return obj[1]; });
        var minValue = Math.min.apply(null, onlyValues),
            maxValue = Math.max.apply(null, onlyValues);

        // create color palette function
        // color can be whatever you wish
        var paletteScale = d3.scale.linear()
            .domain([minValue, maxValue])
            .range(["#fff", "#026292"]); // blue color

        // fill dataset in appropriate format
        data.forEach(function(item){ //
            // item example value ["USA", 70]
            var iso = item[0],
                value = item[1];
            dataset[iso] = { numberOfThings: value, fillColor: paletteScale(value) };
        });

        // render map
        new Datamap({
            element: document.getElementById(containerSelector.replace("#", "")),
            projection: 'mercator', // big world map
            // countries don't listed in dataset will be painted with this color
            fills: { defaultFill: '#fff' },
            data: dataset,
            geographyConfig: {
                borderColor: '#e6f7ff',
                highlightBorderWidth: 1,
                // don't change color on mouse hover
                highlightFillColor: function(geo) {
                    return geo['fillColor'] || '#fff';
                },
                // only change border
                highlightBorderColor: '#ccefff',
                // show desired information in tooltip
                popupTemplate: function(geo, data) {
                    // don't show tooltip if country don't present in dataset
                    if (!data) { return ; }
                    // tooltip content
                    return ['<div class="hoverinfo">',
                        '<strong>', geo.properties.name, '</strong>',
                        '<br>Count: <strong>', data.numberOfThings, '</strong>',
                        '</div>'].join('');
                }
            }

        });
    });

}
