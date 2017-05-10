function drawSvg(data) {

    //http://jsfiddle.net/amcharts/W6Dw8/
    //https://live.amcharts.com/new/edit/	

    var params = {
        "type": "pie",
        "balloonText": "[[stranka]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
        "colors": [
            "red",
            "blue",
            "orange"
        ],
       /* "allLabels": [
		{
			"align": "right",
			"bold": true,
			"id": "Label-1",
			"text": "Labela"
		}],*/
        "titleField": "skracenica",
        "valueField": "procenat",
        "someField": "stranka",
        "innerRadius": "40%",
        "labelRadius": 23,
        "radius": "35%",
        "allLabels": [],
        "balloon": {},
        "fontSize": 13,
        "legend": {

            "enabled": true,
            "fontSize": 16,
            "align": "right",
            "left": -3,
            "markerType": "circle",
            "position": "right",
            "listeners": [],
            "switchable": false

        },
        "titles": [],
        "dataProvider": data,
        "listeners": [{
            "event": "drawn",
            "method": addLegendLabel
        }],
        "export": {
		    "enabled": true
		  }
    }


    AmCharts.makeChart('viz', params);


    function hover(e) {
        //e.dataItem.dataContext.stranka

    }

    function addLegendLabel(e) {
        var title = document.createElement("div");
        title.innerHTML = "Stranke";
        title.className = "legend-title";
        e.chart.legendDiv.appendChild(title);
        $(e.chart.legendDiv).css('height', '160px')
        $(e.chart.legendDiv).find('svg').css('margin-top', '20px');
    }
}
