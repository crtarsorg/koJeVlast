<!DOCTYPE html>
<html>
<head>
    <title>Mapa preletača</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
    <script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
    <style>
        html, body{
            margin: 0;
            padding: 0;

            min-width: 100%;
            width: 100%;
            max-width: 100%;

            min-height: 100%;
            height: 100%;
            max-height: 100%;
        }
        #map { width: 100%; height: 100%; }
        body { font: 16px/1.4 "Helvetica Neue", Arial, sans-serif; }
        .ghbtns { position: relative; top: 4px; margin-left: 5px; }
        a { color: #0077ff; }
        .leaflet-shadow-pane{
            display: none;
        }
        .leaflet-marker-pane{
            display: none;
            -webkit-animation: fadein 4s; /* Safari and Chrome */
            -moz-animation: fadein 4s; /* Firefox */
            -ms-animation: fadein 4s; /* Internet Explorer */
            -o-animation: fadein 4s; /* Opera */
            animation: fadein 4s;
        }

    </style>
</head>
<body>



<div id="map"></div>

<!-- <script src="../node_modules/simpleheat/simpleheat.js"></script>
<script src="../src/HeatLayer.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

<script src="http://www.openlayers.org/api/OpenLayers.js"></script>

<script>
var map = L.map('map').setView([44.7995311, 20.475025], 8 );

        map.on("moveend", function() {
            var zoom = map.getZoom();
            console.dir(zoom);
            if(zoom>8) {$(".leaflet-marker-pane").fadeIn(500);} else {$(".leaflet-marker-pane").fadeOut(500);}
        });

var tiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);
$.ajax({
     //url: 'http://kojenavlasti.rs/api/preletaciPoOpstinama',
     url: "http://"+window.location.hostname+"/api/preletaciPoOpstinama",
 })
 .done(function(data) {
    data = JSON.parse(data);

    addressPoints = data.map(function (p) { return [p.lat, p.lng,p.brPreletaca /13.0,p.Osoba]; });


// add markers

         //Loop through the markers array
         for (var i=0; i<addressPoints.length; i++) {

            var lon = addressPoints[i][1];
            var lat = addressPoints[i][0];
            var popupText = addressPoints[i][3];

             var markerLocation = new L.LatLng(lat, lon);
             var marker = new L.Marker(markerLocation);
             map.addLayer(marker);

             marker.bindPopup(popupText);

         }


    var heat = L.heatLayer(addressPoints,  {radius: 20,  minOpacity:0.7, maxZoom:5,gradient:{0.3: 'blue', 0.5: 'lime', 0.9: 'red'}}).addTo(map);



 })
 .fail(function() {
     console.log("error");
 })
 .always(function() {
     console.log("complete");
 });
</script>
</body>
</html>