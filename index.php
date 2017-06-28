<!DOCTYPE html>
<html lang="en">

<head>

	<title>Ko je na vlasti</title>

	<?php include_once 'partials/head-part.html'; ?>

</head>

<body>

	<?php
		include_once 'partials/nav.html';
	?>

    <div class="container-fluid baner-container">

        <a href="statistike.php">
        <div class="banner">
            <img src="static/icons/preletaci.svg">
            <div class="banner-text">Pregledajte najaktivnije preletače i ostale statistike <span class="glyphicon glyphicon-play" aria-hidden="true"></span></div>
        </div>
        </a>
    </div>
    <div class="container-fluid" id="mainWrapper">
        <div id="mapa" class="col-lg-8"></div>
        <div id="indikator" class="col-lg-4"> </div>
        <div id="opis" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 hidden">
            <div id="detalji" class="clear">
                <h3 class="detalji_title"></h3>
                <span>Broj odbornika u lokalnoj skupštini</span>
                <table class="table table-striped">
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="spinner" class="hidden">
        <img src="static/default.svg">
    </div>
    <div id="fade" class="hidden"></div>
    <!-- <script src="static/anotate.js"></script> -->

    <script src="static/data.js?2"></script>
    <script src="static/helpers.js?4"></script>
    <script src="static/eventHandlers.js?2"></script>
    <script src="static/script-index.js?10"></script>


    <script>
        $(".baner-container").delay(15000).fadeOut(300);
    </script>

</body>

</html>
