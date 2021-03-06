<!DOCTYPE html>
<html lang="en">

<head>

    <title>Ko je na vlasti</title>

    <?php include_once 'partials/head-part.html'; ?>

</head>

<body>

    <?php include 'partials/nav.html'; ?>

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

        <div class="odbornici-ukupno">
            <h3>Broj odbornika u vlasti/opoziciji</h3>
                <p><span>Vlast: </span> <span id="br_od_vlast"></span></p>
                <p><span>Opozicija: </span><span id="br_od_op"></span></p>
        </div>


    </div>




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

        $.get('http://'+window.location.hostname+'/api/stats', function(data) {
        	data = JSON.parse(data);
        	data = data.data
        	//console.log( data );

            $("#br_od_vlast").html(data.akteri_aktivni_vlast);
            $("#br_od_op").html(data.akteri_aktivni_opozicija);

			//$("#brPromena").html(data.promena);

        	$("#opstine").html(data.opstina);
        	//$("#regioni").html(data.regiona);


        	$("#muskarci").html(data.muskaraca);
        	$("#zene").html(data.zena);


            $("#stranke").html(data.partija);
            $("#odbNoInfo").html(data.bezStranka);
            $("#odbInfo").html(data.saStranka);

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
                    "initComplete": function(settings, json) {
                        $(".statistika-spinner").addClass("hidden");
                    },

                    "language": jezik,

			    } );

        });



        $.get("http://"+window.location.hostname+"/api/top5promenaOpstine",top5);

        function top5(data) {

                data = JSON.parse(data);
                for (var i = 0; i < data.length; i++) {

                    var temp = data[i];
                    $("#ulOpPromene").append("<li>" + temp.opstina + "</li>")
                }
            }
        })

        $.get("http://"+window.location.hostname+"/api/preletaci", function (data) {
            data = JSON.parse(data);

            var nek = data.slice(0,5) ;

            for (var i = 0; i < nek.length; i++) {

                $("#ul_preletaci").append("<li><a href='"+'promeneAkter.php?id='+nek[i].id+"'>" + nek[i].ime+ "</a></li>")
            }

            /* data = data.map(function (el) {
                return [el.ime, el.opstina, el.prelet]
            })*/

            var columns = [{
                "title": "Ime i prezime",
                "targets": 0,
                "data": "ime",
                "render": function(data, type, full, meta) {
                        unos = '<a href="promeneAkter.php?id='+full.id +' ">'+ data +'</a>'
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
                    "language": jezik


                } );

        })
    </script>
    </main>
</body>

</html>
