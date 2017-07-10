<!DOCTYPE html>
<html lang="en">

<head>

	<title>Tabele sa podacima</title>

	<?php include_once 'partials/head-part.html'; ?>

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
                <a class="navbar-brand" href="/"><img src="static/icons/logo.svg"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/">Početna</a></li>
                   <!--  <li><a href="partials/uporedjivanje.html">Uporedjivanje opstina</a></li> -->
                    <li class="active"><a href="tabela.php">Tabele sa podacima</a></li>
                    <li><a href="statistike.php">Statistike</a></li>
                    <li><a href="o-nama.php">O nama</a>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="prijavi"><a href="<?php echo 'http://'.$_SERVER["SERVER_NAME"].'/api/posaljitePromenu';?>">Prijavi promenu</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <main>

	<div class="container tabela-container">

        <table id="data" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <th>Id</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Stranka</th>
                <th>Funkcija</th>
                <th>Koalicija</th>
                <th>Opstina</th>
                <th>Datum od</th>
                <th>Datum do</th>
                <th>U vlasti</th>
            </thead>
            <tfoot>
                <th>Id</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Stranka</th>
                <th>Funkcija</th>
                <th>Koalicija</th>
                <th>Opstina</th>
                <th>Datum od</th>
                <th>Datum do</th>
                <th>U vlasti</th>

            </tfoot>
        </table>

    </div>

    <script>
    $(document).ready(function() {
    //load data
        $('#data').DataTable( {
            "responsive": true,
        	"processing": true,
           
            dom: 'lBfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            "search": {
                "regex": true
              },
            "ajax": 'http://'+window.document.location.hostname + '/api/akteri',
            "language":jezik,
            "initComplete": function( settings, json ) {
               var params = window.location.search
               if(params.length > 5 ){
                    params = decodeURIComponent (params.split("?osoba=")[1]);
                    $(this[0]).DataTable().search(params).draw()
               }
              }
        } );

    return;

    });
    </script>

    <?php include_once "footer.php"; ?>