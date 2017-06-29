<!DOCTYPE html>
<html lang="en">

<head>

    <title>Ko je na vlasti</title>

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
                    <li><a href="tabela.php">Tabele sa podacima</a></li>
                    <li class="active"><a href="statistike.php">Statistike</a></li>
                    <li><a href="o-nama.php">O nama</a>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="prijavi"><a href="posaljite-promenu.php">Prijavi promenu</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <main>
    
    <div class="statistika-spinner">
        <div id="statistika-spinner">
            <img src="static/default.svg">
        </div>
    </div>

    <div class="container">
        <div class="baneri-highlight">

            <div class="highlight-container col-md-4">
                <img src="static/icons/stats_2.svg" alt="">
                <div class="highlight">
                    <h3>Top 5 preletača</h3>
                    <div>
                        <ul id="ul_preletaci">

                        </ul>
                    </div>
                </div>
            </div>

            <div class="highlight-container col-md-4">
                <img src="static/icons/stats1.svg" alt="">
                <div class="highlight">
                    <h3>Najčešće stranke u vlasti</h3>
                    <div>
                        <ul id="ul_stranke">

                        </ul>
                    </div>
                </div>
            </div>

            <div class="highlight-container col-md-4">
                <img src="static/icons/stats_3.svg" alt="">
                <div class="highlight">
                    <h3>Opštine sa najviše promena</h3>
                    <div>
                        <ul id="ulOpPromene">

                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="statsrownice">

            <!-- <div class="col-lg-2 statboxnice">
                <span id="brPromena" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Ukupan broj promena
                    <sup>
                        <i class="fa fa-question-circle" data-box="hpromena" aria-hidden="true"></i>
                    </sup>
                </h3>
            </div> -->


            <div class="col-lg-2 statboxnice">
                <span id="opstine" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj opština<sup><i class="fa fa-question-circle" data-box="hopstina" aria-hidden="true"></i></sup> </h3>

            </div>

            <div class="col-lg-2 statboxnice">
                <span id="odbInfo" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj odbornika sa informacijama
                    <sup>
                        <i class="fa fa-question-circle" data-box="hinfo" aria-hidden="true"></i>
                    </sup>
                </h3>
            </div>

            <div class="col-lg-2 statboxnice">
                <span id="odbNoInfo" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj odbornika bez informacija
                    <sup>
                        <i class="fa fa-question-circle" data-box="hnoinfo" aria-hidden="true"></i>
                    </sup>
                </h3>
            </div>

            <div class="col-lg-2 statboxnice">
                <span id="muskarci" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj muškaraca<sup><i class="fa fa-question-circle" data-box="hm" aria-hidden="true"></i></sup> </h3>
            </div>

            <div class="col-lg-2 statboxnice">
                <span id="zene" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj žena<sup><i class="fa fa-question-circle" data-box="hz" aria-hidden="true"></i></sup>  </h3>
            </div>

            <div class="col-lg-2 statboxnice">
                <span id="stranke" class="stat-number"></span>
                <div class="triangle"></div>
            	<h3>Stranke<sup><i class="fa fa-question-circle" data-box="hstr" aria-hidden="true"></i></sup> </h3>
            </div>
        </div>


        <div class="statsrow">
        </div>

        <div class="preletaci-tabela">
            <h3>Preletači</h3>
            <table id="preletaciTabela">

            </table>
        </div>

        <div class="stranke-tabela">
            <h3>Vlast/opozicija po strankama</h3>
            <table id="strankeTabela">

            </table>
        </div>

    </div>

<!--
<div id="explain" class="modal fade" role="dialog">
    <div class="modal-dialog">


        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
 -->


    <script src="static/helpers.js?5"></script>


    <script type="text/javascript">

        function onlyUnique(value, index, self) { 
    	    return self.indexOf(value) === index;
    	}
        //http://kojenavlasti.rs/api/stats
        $(function() {


        $(".statsrownice .fa-question-circle").on("click", function(e){
            resetPopups();

            var sup = $(this).parent();
            tar = e.currentTarget.dataset.box;

            var text;
            if(tar=="hpromena") text = "Ovaj broj se odnosi na ukupan broj promena funkcija u lokalnoj samoupravi, promena stranaka, promena koalicija...";
            if(tar=="hopstina") text = "Ovaj broj se odnosi na ukupan broj opština u kojima su zabeležene promene";
            if(tar=="hm") text = "Objašnjenje za broj muškaraca";
            if(tar=="hz") text = "Objašnjenje za broj žena";
            if(tar=="hinfo") text = "Broj odbornika za koje su dostupne  informacije o pripadnosti stranci";
            if(tar=="hnoinfo") text = "Broj odbornika za koje nisu dostupne  informacije o pripadnosti stranci";
            if(tar=="hstr") text = "Objašnjenje za stranke";

            sup.prepend("<div class='popup'><span class='popup-close'><i class='fa fa-times' aria-hidden='true'></i></span><p>" +  text + "</p></div>");

            closePopup();

        });

        function prveStranke( podaci ) {
            var c = 0;

            for (var i = 0; i < podaci.length; i++) {
                if( c >= 5)return;

                var naziv = podaci[i][0];

                if(["Stranka nije na listi","Nepoznata"].indexOf(naziv) ==-1){
                    $("#ul_stranke").append("<li>"+naziv+"</li>");
                    c++;

                }
            }
        }

        $.get('http://kojenavlasti.rs/api/stats', function(data) {
        	data = JSON.parse(data);
        	data = data.data
        	//console.log( data );

			//$("#brPromena").html(data.promena);

        	$("#opstine").html(data.opstina);
        	//$("#regioni").html(data.regiona);


        	$("#muskarci").html(data.muskaraca);
        	$("#zene").html(data.zena);


            $("#stranke").html(data.partija);
            $("#odbNoInfo").html(data.akteri_aktivni_bez_statusa);
            $("#odbInfo").html(data.akteri_aktivni_vlast+data.akteri_aktivni_opozicija);

        	var zajedno = {};

        	var vlast_stranke  =
				Object.keys(
					data.akteri_aktivni_vlast_stranka);

			var opozicija_stranke  =
				Object.keys(
					data.akteri_aktivni_opozicija_stranka);

			var sve_stranke = vlast_stranke.concat(opozicija_stranke)

        	/*zajedno.vlast = data.akteri_aktivni_vlast_stranka
        		;
        	zajedno.opozicija =
        		data.akteri_aktivni_opozicija_stranka;*/


        	sve_stranke = sve_stranke.filter(onlyUnique)

        	var res = [];
        	for (var i = 0; i < sve_stranke.length; i++) {

        		var temp = {};
        		var temp1 = []
        		temp.naziv = sve_stranke[i];
        		temp.vlast = data.akteri_aktivni_vlast_stranka[sve_stranke[i]] || 0 ;
        		temp.opozicija = data.akteri_aktivni_opozicija_stranka[sve_stranke[i]] || 0 ;

        		temp1 =
        			[temp.naziv,
        			temp.vlast,
        			temp.opozicija ];

        		res.push(temp1)
        	}

            var na_vlasti = res.slice( 0, 10 );
            prveStranke(na_vlasti);


        	$('#strankeTabela').DataTable( {
			        data: res,
			        columns: [
			            { title: "Stranka" },
			            { title: "Broj odbornika u vlasti" },
			            { title: "Broj odbornika u opoziciji" }

			        ],
<<<<<<< HEAD
                    "language": {
                        "search": "Pretražite:",
                        "lengthMenu": "Prikazano _MENU_ unosa po strani",
                        "zeroRecords": "Nema unosa ",
                        "info": "Prikazana strana _PAGE_ od _PAGES_",
                        "infoEmpty": "Nema unosa",
                        "infoFiltered": "(filtrirano od dostupnih _MAX_ unosa)"
                      },
                    "initComplete": function(settings, json) {
                        $(".statistika-spinner").addClass("hidden");
                    }
=======
                    "language": jezik,
>>>>>>> origin/master

			    } );

        });


        //$.get("http://kojenavlasti.rs/api/imajuPromene", preletaci_f)

        /*var preletaci_f = function (data) {
            //data = JSON.parse(data);
            var prvih_5 = data.slice(0,5);

            for (var i = 0; i < prvih_5.length; i++) {
                var la = prvih_5[i].id;
                $.get("http://kojenavlasti.rs/api/akter/promene/"+la, function(d) {
                    d = JSON.parse(d);
                    d = d[0];
                    $("#ul_preletaci").append("<li><a href='"+'tabela.php?osoba='+d.id+"'>" + d.aime + " " +d.aprezime + "</a></li>")
                    //console.log(d[0]);
                })
            }

            //prodji za svakog i nabavi ime
        };*/

        $.get("http://kojenavlasti.rs/api/top5promenaOpstine",top5);

        function top5(data) {

                data = JSON.parse(data);
                for (var i = 0; i < data.length; i++) {

                    var temp = data[i];
                    $("#ulOpPromene").append("<li>" + temp.opstina + "</li>")
                }
            }
        })

        $.get("http://kojenavlasti.rs/api/preletaci", function (data) {
            data = JSON.parse(data);

            var nek = data.slice(0,5) ;

            for (var i = 0; i < nek.length; i++) {

                $("#ul_preletaci").append("<li><a href='"+'tabela.php?osoba='+nek[i].id+"'>" + nek[i].ime+ "</a></li>")
            }

            /* data = data.map(function (el) {
                return [el.ime, el.opstina, el.prelet]
            })*/

            var columns = [{
                "title": "Ime i prezime",
                "targets": 0,
                "data": "ime",
                "render": function(data, type, full, meta) {
                        unos = '<a href="tabela.php?osoba='+data +'" >'+ data +'</a>'
                    return unos;
                }

            }, {
                "title": "Opština",
                "targets": 1,
                "data": "opstina",
                "render": function(data, type, full, meta) {
                    return data;
                }

            }, {
                "title": "Broj stranaka",
                "targets": 2,
                "data": "prelet",
                "render": function(data, type, full, meta) {
                    return data;
                }

            }];

            $('#preletaciTabela').DataTable( {
                    data: data,
                    "columnDefs": columns,
                    "order": [2, "desc"],
                    "language": jezik,

                } );

        })
    </script>
    </main>
</body>

</html>
