<?php


$post_id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<title></title>

	<?php include_once 'partials/head-part.html'; ?>

	<style type="text/css">
	.vest-entry{
	    border-bottom: 1px solid;
	    padding: 5px;
	    margin: 5px 0;
	}
	</style>
</head>
<body>

<?php include_once 'partials/nav.html'; ?>

<div id="main" class="container-fluid col-lg-10 col-lg-offset-1 ">
	<h1 class="mx-auto">Vesti</h1>
	<div id="container" class="">
		<?php


		function curl_get_contents($url)
		{
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);

		    $data = curl_exec($ch);
		    curl_close($ch);

		    return $data;
		}


		$posts = curl_get_contents('http://vesti.kojenavlasti.rs/wp-json/wp/v2/posts/'.$post_id);
		$posts = json_decode($posts);
		$temp =
				'<div class="vest-entry">'
			.'<h3>'.$posts->title->rendered.'</h3>'
			. $posts->content->rendered
			.'</div>';


		echo "$temp";
		?>
	</div>

</div>

</main>
</body>
</html>