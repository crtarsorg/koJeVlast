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

    <div class="container">
        <div class="baneri-highlight">
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
            <div class="col-lg-2 statboxnice">
                <span id="brPromena" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Ukupan broj promena
                    <sup>
                        <i class="fa fa-question-circle" data-box="hpromena" aria-hidden="true"></i>
                    </sup>
                </h3>
            </div>

            <div class="col-lg-2 statboxnice">
                <span id="opstine" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj opština<sup><i class="fa fa-question-circle" data-box="hopstina" aria-hidden="true"></i></sup> </h3>

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
                <span id="regioni" class="stat-number"></span>
                <div class="triangle"></div>
                <h3>Broj regiona<sup><i class="fa fa-question-circle" data-box="hreg" aria-hidden="true"></i></sup> </h3>

            </div>

            <div class="col-lg-2 statboxnice">
                <span id="stranke" class="stat-number"></span>
                <div class="triangle"></div>
            	<h3>Stranke<sup><i class="fa fa-question-circle" data-box="hstr" aria-hidden="true"></i></sup> </h3>
            </div>
        </div>


        <div class="statsrow">
        </div>

        <div class="stranke-tabela">
            <h3>Akteri po strankama i statusima</h3>
            <table id="strankeTabela">

            </table>
        </div>

    </div>


<div id="explain" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
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
            if(tar=="hreg") text = "Objašnjenje za broj regiona";
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

        $.get('../api/stats', function(data) {
        	data = JSON.parse(data);
        	data = data.data
        	//console.log( data );

			$("#brPromena").html(data.promena);

        	$("#opstine").html(data.opstina);
        	$("#regioni").html(data.regiona);


        	$("#muskarci").html(data.muskaraca);
        	$("#zene").html(data.zena);

        	$("#stranke").html(data.partija);


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
			            { title: "stranka" },
			            { title: "vlast" },
			            { title: "opozicija" }

			        ],
                    "language": {
                        "search": "Pretražite:",
                        "lengthMenu": "Prikazano _MENU_ unosa po strani",
                        "zeroRecords": "Nema unosa ",
                        "info": "Prikazana strana _PAGE_ od _PAGES_",
                        "infoEmpty": "Nema unosa",
                        "infoFiltered": "(filtrirano od dostupnih _MAX_ unosa)"
                      },

			    } );

        });


        $.get("http://kojenavlasti.rs/api/imajuPromene", function (data) {
            data = JSON.parse(data);
            var prvih_5 = data.slice(0,5);

            for (var i = 0; i < prvih_5.length; i++) {
                var la = prvih_5[i].id;
                $.get("http://kojenavlasti.rs/api/akter/promene/"+la, function(d) {
                    d = JSON.parse(d);
                    d = d[0];
                    $("#ul_preletaci").append("<li><a href='"+'http://kojenavlasti.rs/partials/tabela.html?osoba='+d.aime + " " +d.aprezime+"'>" + d.aime + " " +d.aprezime + "</a></li>")
                    //console.log(d[0]);
                })
            }

            //prodji za svakog i nabavi ime
        })

        $.get("http://kojenavlasti.rs/api/top5promenaOpstine",top5);

        function top5(data) {

            data = JSON.parse(data);
            for (var i = 0; i < data.length; i++) {

                var temp = data[i];
                $("#ulOpPromene").append("<li>" + temp.opstina + "</li>")
                    //console.log(d[0]);

            }

        }


    })
    </script>
    </main>
</body>

</html>
