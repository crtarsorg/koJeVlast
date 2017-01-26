/*d3 v4 */
/*canvas - https://bl.ocks.org/mbostock/2394b23da1994fc202e1*/
//convert svg to canvas and to png https://bl.ocks.org/biovisualize/8187844

function draw() {

    var canvas = document.querySelector("canvas"),
        context = canvas.getContext("2d");

    var width = canvas.width,
        height = canvas.height,
        radius = Math.min(width, height) / 2;

    var colors = ["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"];

    var arc = d3.arc()
        .outerRadius(radius - 10)
        .innerRadius(radius - 70)
        .context(context);

    var labelArc = d3.arc()
        .outerRadius(radius - 40)
        .innerRadius(radius - 40)
        .context(context);

    var pie = d3.pie()
        .sort(null)
        .value(function(d) {
            return d.population;
        });

    context.translate(width / 2, height / 2);

    d3.tsv("viz/data.tsv", function(d) {
        d.population = +d.population;
        return d;
    }, function(error, data) {
        if (error) throw error;

        var arcs = pie(data);

        arcs.forEach(function(d, i) {
            context.beginPath();
            arc(d);
            context.fillStyle = colors[i];
            context.fill();
        }); //foreach arcs

        context.beginPath();
        arcs.forEach(arc);
        context.strokeStyle = "#fff";
        context.stroke();

        context.textAlign = "center";
        context.textBaseline = "middle";
        context.fillStyle = "#000";
        arcs.forEach(function(d) {
            var c = labelArc.centroid(d);
            context.fillText(d.data.age, c[0], c[1]);
        }); //foreach arcs
    }); //request tsv
} //draw

//http://blockbuilder.org/vickygisel/c3f4eb2b16b86dd0f641263383f05a13
function drawSvg( podaci ) {


	 /*var dataset = [
          	{ age: "lactantes", population: 74},
      		{ age: "deambuladores", population: 85},
      		{ age: "2 años", population: 840},
	 		{ age: "3 años", population: 4579 }, 
	 		{ age: "4 años", population: 5472 }, 
	 		{ age: "5 años", population: 7321 },

        ];*/

	d3.selectAll('#viz svg').remove()
	d3.selectAll('.legenda svg').remove()

    var color = d3.scaleOrdinal( d3.schemeCategory20b );

    var width = 250;
    var height = 250;
    var radius = Math.min( width, height ) / 2;

    var svg = d3.select('#viz')
    	.style('width', width+"px")
        .style('float', "left")
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .append('g')
        .attr('transform', 'translate(' + (width / 2) +
            ',' + (height / 2) + ')');

    var legendaCont = d3.select('.legendaCont')
    	.style('display', "inline-block")
    	.style('float', "right")
    	.style('height', height+"px")
    	.style('width', (width - 200)+"px")

    var legendaCont = d3.select('.legenda')
        .append('svg')
        /*.attr('width', width)*/
        .attr('height', height)
        .append('g')
        .attr('transform', 'translate('+ '50,100'+ /*+ (width / 2) +
            ',' + (height / 2) + */')');    

    var colors = ["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"];

    var arc = d3.arc()
        .outerRadius(radius - 10)
        .innerRadius(radius - 70)
        .padAngle(0.01);

    var labelArc = d3.arc()
        .outerRadius(radius - 40)
        .innerRadius(radius - 40);

    var pie = d3.pie()
        .sort(null)
        .value(function(d) {
            return d.procenat;
        });



    //legend

    var legendRectSize = 18;
    var legendSpacing = 4;




    d3.tsv("viz/data.tsv", function(d) {
        d.population = +d.population;
        return d;
    }, function(error, data) {
        if (error) throw error;

        data = podaci;

        /*if(tipPodataka == "stranke"){
        	data = data;
        }
        else{
			data = dataset;
        }*/

        var path = svg.selectAll('path')
            .data(pie(data))
            .enter()
            .append('path')
            .attr('d', arc)
            .attr('fill', function(d, i) {
                return color( d.data.procenat );
            })
            .on("click",function (d,i) {
            	//alert("sada")
            })
            .on("mouseover",function (d,i) {
            	var levo = d3.select(this);            	
            	levo.attr('class', 'pomeren');
            })
            .on("mouseout",function (d,i) {
            	var levo = d3.select(this);  
            	levo.classed('pomeren',false);

            });

        var legend = legendaCont.selectAll('.legenda')
            .data(color.domain())
            .enter()
            .append('g')
            .attr('class', 'legend')
            .attr('transform', function(d, i) {
                var height = legendRectSize + legendSpacing;
                var offset = height * color.domain().length / 2;
                var horz = -2 * legendRectSize;
                var vert = i * height - offset;
                return 'translate(' + horz + ',' + vert + ')';
            })
            .attr('cursor', 'pointer')
            .on('click',  function(d,i) {
            	
            	d3.selectAll("#viz path")
            		.attr("opacity","1");

            	d3.selectAll("#viz path")
            		.filter(function(dat,ind){
            			return dat.value!= d 
            		})
            		.attr("opacity","0.3");

            });

        legend.append('rect')
            .attr('width', legendRectSize)
            .attr('height', legendRectSize)
            .style('fill', color)
            .style('stroke', color);

        legend.append('text')
            .data(pie(data))
            .attr('x', legendRectSize + legendSpacing)
            .attr('y', legendRectSize - legendSpacing)
            .text(function(d) {
                /*console.log(d);*/
                return d.data.skracenica;
            });


            
		$('a.tab-filter[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		  var target = $(e.target).attr("href") // activated tab

		  //uradi update grafa
		  
		  drawSvg(["stranke","koalicija"][parseInt(Math.floor(Math.random() * 2))] );
		});

    }); // tsv load
}


