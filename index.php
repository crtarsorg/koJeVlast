<!DOCTYPE html>
<html lang="en">

<head>

	<title>Ko je na vlasti</title>

	<?php include_once 'partials/head-part.html'; ?>

    <style type="text/css">
    body {
        /* or http://lea.verou.me/css3patterns/ */
        background: url("./static/body-bg.jpg") !important;
        height: 100%;
    }
    
    #opis {
        max-height: 300px;
        margin-top: 100px;
    }
    #spinner{
        top: 30%;
        left:40%;
        position: fixed;
        z-index: 5;
    }
    #fade{
        height: 100%;
        width: 100%;
        background-color: rgba(128, 128, 128, 0.78);
        top: 0;
        position: absolute;
    }
    </style>
</head>

<body>

	<?php 
		include_once 'partials/nav.html';
	?>	


    <div class="container-fluid" id="mainWrapper">
        <div id="mapa" class="col-lg-8"></div>
        <div id="indikator" class="col-lg-4"> </div>
        <div id="opis" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div id="detalji" class="clear" >
                <h3 class="detalji_title">Stranke u vlasti</h3>
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
    
    <script src="static/data.js?1"></script>
    <script src="static/helpers.js?1"></script>
    <script src="static/script-index.js?2"></script>


</body>

</html>
