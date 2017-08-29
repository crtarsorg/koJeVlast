<!DOCTYPE html>
<html lang="en">

<head>

	<title>Vesti</title>

	<?php include_once 'partials/head-part.html'; ?>

</head>
<body>

<?php include_once 'partials/nav.html'; ?>

<div id="main" class="container-fluid col-lg-10 col-lg-offset-1 ">
	<h1 class="mx-auto">Vesti</h1>
	<div id="container" class="">

	</div>

</div>

<script type="text/javascript">

	(function($){
		$(function (argument) {

			$.ajax({
				url: 'http://vesti.kojenavlasti.rs/wp-json/wp/v2/posts',
			})
			.done(function( la ) {

				prikazi( la );
				//console.log( la[0].content.rendered );
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});


			function prikazi( odgovor ) {
				for (var i = 0; i < odgovor.length; i++) {
					var temp = `<div>${odgovor[i].content.rendered}</div>`;
					$("#container").append(temp)
				}
			}

		})
	})(jQuery)
</script>

</main>
</body>
</html>