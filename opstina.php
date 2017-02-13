<?php  

	
	
	$la = $_GET["naziv"];
	$id = $_GET["id"];
	//treba naziv i id
	
	// povuci podatke za tu opstinu
	// iskorisiti postojeci kod za popunjavanje; on je u js-u


?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Opstina</title>

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

<script src="static/script-index.js"></script>

<script type="text/javascript">
	

	<?php 
	//kako dobiti ovaj idlazi se samo na mapi
	echo "var idOpstine = $id;\n";
	
	?>
	$.getJSON(BASE_PATH +"api/opstine", function(json, textStatus) {
	    DataStranke.setOpstine(json);

	    opstinaDetalji(idOpstine);
	});


	$(".modal").removeClass("fade")
	$(".modal").removeClass("modal")

</script>


</body>
</html>