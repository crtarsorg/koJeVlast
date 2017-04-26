function anotate() {


} 





/*// create a container for tooltips
tipg = svg.append("g")
    .attr("class", "annotation-tip");

// this function will call d3.annotation when a tooltip has to be drawn
function tip(d) {
    annotationtip = d3.annotation()
        .type(d3.annotationCalloutCircle)
        .annotations([d].map(d => {
            return {
                data: d,
                dx: d.dx || (d.x > 450) ? -50 : 50,
                dy: d.dy || (d.y > 240) ? -10 : 10,
                note: {
                    label: d.name || "??",
                },
                subject: {
                    radius: d.r,
                    radiusPadding: 2,
                },
            };
        }))
        .accessors({ x: d => projection(d)[0], y: d => projection(d)[1] })
    tipg.call(annotationtip);
}*/


/*d3.select("svg")
  .append("g")
  .attr("class", "annotation-group")
  .call(makeAnnotations)*/
