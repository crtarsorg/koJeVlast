<!DOCTYPE html>
<html lang="en">

<head>

	<title>Tabele sa podacima</title>

	<?php include_once 'partials/head-part.html'; ?>
    <!-- <script src="//cdn.datatables.net/plug-ins/1.10.16/filtering/type-based/accent-neutralise.js"></script>
    https://datatables.net/plug-ins/filtering/type-based/accent-neutralise#CDN -->
</head>

<body>

<?php include 'partials/nav.html'; ?>

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
    var table =     $('#data').DataTable( {
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



    $('input[type="search"]').on( 'keyup click', function () {
        var param = this.value.replace(/S|s|C|c|z|Z/g,".");
        table.search( param ).draw();
    } );

    //return;

    });
    </script>

    <?php include_once "footer.php"; ?>