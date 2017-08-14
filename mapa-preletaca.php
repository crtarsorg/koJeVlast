<!DOCTYPE html>
<html>
<head>
    <title>Mapa preletača</title>


<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <script src="admin/js/jquery-1.11.2.js" ></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>


    <!-- bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css">



    <!-- stilovi  -->
    <link rel="stylesheet" type="text/css" href="static/style-index.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.1/awesomplete.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>


        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-101911387-1', 'auto');
            ga('send', 'pageview');

        </script>



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
    .leaflet-top {
        top: 60px;
    }
    .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background: white;
        box-shadow: 0 3px 14px rgba(98,166,152,0.9);
        border: 0px solid #62a698;
    }
    .leaflet-container a.leaflet-popup-close-button {
        color: #62a698;
    }
    .leaflet-container a {
        color: #62a698;
    }
    .leaflet-container h3 {
        color: #62a698;
        border-bottom: 2px solid #62a698;
    }

    </style>
</head>
<body>


   <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><img src="../static/icons/logo.svg"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/">Početna</a></li>
                   <!--  <li><a href="partials/uporedjivanje.html">Uporedjivanje opstina</a></li> -->
                    <li><a href="tabela.php">Tabele sa podacima</a></li>
                    <li><a href="statistike.php">Statistike</a></li>
                    <li><a href="o-nama.php">O nama</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="prijavi"><a href="http://kojenavlasti.rs/api/posaljitePromenu">Prijavi promenu</a></li>
                </ul>

            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>




<div id="map"></div>




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

    addressPoints = data.map(function (p) { return [p.lat, p.lng,p.brPreletaca /13.0,p.Osoba,p.opstina]; });


// add markers

         //Loop through the markers array
         for (var i=0; i<addressPoints.length; i++) {

            var lon = addressPoints[i][1];
            var lat = addressPoints[i][0];
            var popupText =  '<h3>'+addressPoints[i][4]+'</h3>'+ addressPoints[i][3];

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