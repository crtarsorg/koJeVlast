<!DOCTYPE html>
<html lang="en">

<head>

	<title>Ko je na vlasti</title>

	<?php include_once 'partials/head-part.html'; ?>


    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
    <script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
    <style>

    #map { 
        width: 100%;
        min-width: 10px;
        min-height: 1px;
    }

    .height100{
        height: 100vh
    }
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

	<?php include 'partials/nav.html'; ?>

   <!--  <div class="container-fluid baner-container">

       <a href="statistike.php">
       <div class="banner">
           <img src="static/icons/preletaci.svg">
           <div class="banner-text">Pregledajte najaktivnije preletače i ostale statistike <span class="glyphicon glyphicon-play" aria-hidden="true"></span></div>
       </div>
       </a>
   </div> -->

    <div>
        <h3>Prebacite na mapu preletača</h3>
        <input type="checkbox" name="my-checkbox" >
    </div>

    <div class="container-fluid" id="mainWrapper">
        <div id="mapa" class="col-lg-8"></div>
        <div id="indikator" class="col-lg-4"> </div>
        <div id="opis" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 hidden">
            <div id="detalji" class="clear">
                <h3 class="detalji_title"></h3>
                <span>Broj odbornika na vlasti (za koje su informacije dostupne)</span>
                <table class="table table-striped">
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>




    </div>
    <div class="container-fluid " id="mapaWrap">

        <div id="map" class=""></div>
    </div>

    <div id="spinner" class="hidden">
        <img src="static/default.svg">
    </div>
    <div id="fade" class="hidden"></div>
    <!-- <script src="static/anotate.js"></script> -->





    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>

    <script>

    /*heat mapa preletaca*/
    $(function() {
        var heap;
        var map;

        function setMap(m2) {
            map = m2
        }
        function getMap(){
            return map;
        }
        $("[name='my-checkbox']").bootstrapSwitch();

       $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {

          console.log(state); // true | false
          //if(state){
            m1 = getMap();

            $("#map").toggleClass("height100")
            $("#mainWrapper").toggleClass("hidden")

             m1.invalidateSize()
          //}
        });


       function setHeap(m, addressPoints) {
        return function () {
            var heat = L.heatLayer(addressPoints,  {radius: 20,  minOpacity:0.7, maxZoom:5,gradient:{0.3: 'blue', 0.5: 'lime', 0.9: 'red'}}).addTo(m);
           
        }

       }

    map = L.map('map').setView([44.7995311, 20.475025], 8 );

            map.on("moveend", function() {
                var zoom = map.getZoom();
                console.dir(zoom);
                if(zoom>8) {$(".leaflet-marker-pane").fadeIn(500);} else {$(".leaflet-marker-pane").fadeOut(500);}
            });

    var tiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    $.ajax({
         url: "http://"+window.location.hostname+"/api/preletaciPoOpstinama",
     })
     .done(function(data) {
        data = JSON.parse(data);

        addressPoints = data.map(function (p) { return [p.lat, p.lng,p.brPreletaca /13.0,p.Osoba,p.opstina]; });

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
        heap = setHeap(map, addressPoints)

        setMap(map);

        if(map.getSize().x > 0) {
            heap()
        }else{
            setTimeout(heap, 1000)
        }



     })
     .fail(function() {
         console.log("error");
     })
     .always(function() {
         console.log("complete");
     });

    }) //on load

    </script>

    <script>
        $(".baner-container").delay(15000).fadeOut(300);
    </script>

<?php include_once "footer.php"; ?>
