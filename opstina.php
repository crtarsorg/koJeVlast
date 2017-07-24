<?php


	$la = $_GET["naziv"];
	$id = $_GET["id"];
	$okrug = $_GET["okrug"];
	//treba naziv i id


	// povuci podatke za tu opstinu
	// iskorisiti postojeci kod za popunjavanje; on je u js-u


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>

	<?php include_once 'partials/head-part.html'; ?>

	<style type="text/css">
		.modal-backdrop.in{display: none;}
		.modal-open{overflow: scroll;}
	</style>
</head>
<body>

<?php

	include_once 'partials/nav.html';

	include_once 'partials/modal.html';


?>


	<script src="static/data.js?2"></script>
    <script src="static/helpers.js?4"></script>
	<script src="static/script-index.js?95905"></script>


<script type="text/javascript">


$('#modal_id').modal({
    backdrop: 'static',
    keyboard: false  // to prevent closing with Esc button (if you want this too)
})

<?php
	//kako dobiti ovaj idlazi se samo na mapi
	if(!empty($id)){
		echo "\tvar idOpstine = $id;\n";
		echo "\tprikazOpstine(idOpstine);\n";
	}

	if(!empty($okrug)){
		echo "\tvar okrug = $okrug;\n";
		echo "\tprikazOkruga( okrug )\n";
	}

?>



	function prikazOpstine( idOpstine) {

	    $.getJSON(BASE_PATH +"api/opstine", function(json, textStatus) {
		    //DataStranke.setOpstine(json);
		    //console.dir(DataStranke);

		    var naslov = "Naslov ";
		    var podaci = DataStranke.getOpstine();
		    var opstina = podaci.filter(function(el) {
		        return +el.opid == idOpstine;
		    })


	        opstina_temp = opstina[0]
	        naslov = opstina_temp.opstina;
	        id = opstina_temp.opid;
	        idopstine = + opstina_temp.oidopstine;

	        podaciOdborniciOpstina(idOpstine); // id opstine
	        naslov_modal(naslov);
	        info_tab(opstina_temp);

	        dokumenta(idOpstine);

	        linkovi(idOpstine, naslov);

	        document.title = naslov;

	        $(".modal").removeClass("fade")
			$(".modal").removeClass("modal")
			$(".modal-open").removeClass("modal-open")

		});


	}


	function prikazOkruga( idOkrug) {


	        podaciAkteriRegion(idOkrug); //ajax zahtev - podaci za tabelu
	        $.getJSON(BASE_PATH +"api/regionInfo/"+idOkrug, function(json, textStatus) {

	        	podaci = JSON.parse(json)[0]

	        	podaci.ologo = "bb7a4496cbe2a3b397a38acda978c2a1e4b77f36.png"
	        	info_tab(podaci);

	        });

	        showModalRegion(podaci.naslov, idOkrug);

	        $(".modal").removeClass("fade")
			$(".modal").removeClass("modal")
			$(".modal-open").removeClass("modal-open")

	}



</script>


</body>
</html>