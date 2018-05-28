<?php  



	

	$servername = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "KNV_2305";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	//preletanja($conn);
	promena_funkcije($conn);


	function preletanja($conn='')
	{
		$sql_query = 'SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as prelet, posebni from ( SELECT posoba, COUNT( DISTINCT pstranka) as broj, CASE WHEN pod < "2017-05-19" and popstina in ( 195, 115 ) THEN "jesu" WHEN pod < "2017-02-14" and popstina in ( 189 )  THEN "jesu"  ELSE "nisu" END AS posebni , popstina FROM promene group by posoba, pstranka ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid WHERE posebni="nisu" group by posoba having prelet > 1 ORDER BY `prelet` DESC';
		$result = $conn->query($sql_query);



		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		       $result1 = $conn->query("SELECT * FROM `promene` WHERE posoba = ".$row['id'] ." ORDER by pod DESC");
		       update_preletanja($result1->fetch_all(MYSQLI_ASSOC), $conn);

		    }
		} else {
		    echo "0 results";
		}
	}
	

	

	//
	function update_preletanja($unosi, $conn)
	{


		//prvi ne gledam, za njega ne unosim nikakve promene
		for ($i=0, $duz = count($unosi) -1 ; $i < $duz ; $i++) { 
			
			$trenutni = $unosi[$i];
			$prethodni = $unosi[$i+1]; //sortirani su unazad



			$stranka_menjano = "";
			$funkcija_menjano = "";
			$oportuno = "";

			if($trenutni['pstranka']!=$prethodni['pstranka']){
				if($trenutni['pstranka'] == 54){ //nezavisni poslanik
					$stranka_menjano = " tip_preleta = 'nezavisni' ";
				}
				else {
					$stranka_menjano = " tip_preleta = 'preletanje' ";
				}
				
			}


			if($trenutni['pfunkcija']!=$prethodni['pfunkcija'] || 
				$trenutni['pfm']!=$prethodni['pfm'] ){
				//ima i mesto funkcije

				$funkcija_menjano = " promena_funkcije = 1 ";
			}

			if ($trenutni['pnavlasti']!=$prethodni['pnavlasti'] && 
				$trenutni['pnavlasti'] == 1 /*vlast*/) {
				$oportuno = " oport_prelet = 1 ";
			}

			$pr_deo = "";

			if(!empty( $stranka_menjano )){
				$pr_deo.= $stranka_menjano.",";
			}
			if(!empty( $funkcija_menjano )){
				$pr_deo.= $funkcija_menjano . ",";
			}
			if(!empty( $oportuno )){
				$pr_deo.= $oportuno . ",";
			}

			$pr_deo=rtrim(trim($pr_deo),",");

			if(empty($pr_deo))
				continue;

			$upit_update  = 
				"UPDATE `promene` SET ". $pr_deo
				." WHERE `pid` = {$trenutni['pid']};"; 

			$result = $conn->query($upit_update);

			/*print_r('<pre>');
			var_dump($upit_update);
			var_dump($result);
			print_r('</pre>');*/
		}
		//gledam pre svega stranku
		//ako je presao u nezavisne poslanike, onda je to status nezavisnog odbornika
		//inace je status preletanje odbornika
		//grupne stvari moraju rucno da se urade

		//ako je promenjena funkcija setuj jedinicu

		//oportuni prelet - ako promenjeno iz opozicije u vlast
		
	
		
		//radi se insert za svaki unos
		//gleda se u odnosu na prethodni
		//proveravaju se i ostali parametri
	}


	function promena_funkcije($conn='')
	{
		//sta se njima update-uje uopste?
		//oni koji menjali stranku, ali su promenili funkciju, opstinu, vlast
		// bas me briga da li su vec obradjeni kroz preletace, odradi ih sada ponovo

		$upit = 'SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as promena_funkcija from ( SELECT posoba, 1 as broj, popstina FROM promene group by posoba, pfunkcija ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid group by posoba having promena_funkcija > 1 ORDER BY `promena_funkcija` DESC';


		$result = $conn->query($upit);



		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		       $result1 = $conn->query("SELECT * FROM `promene` WHERE posoba = ".$row['id'] ." ORDER by pod DESC");
		       $unosi_funkcije = $result1->fetch_all(MYSQLI_ASSOC);
		       

		       //prvi ne gledam, za njega ne unosim nikakve promene
				for ($i=0, $duz = count($unosi_funkcije) -1 ; $i < $duz ; $i++) { 
					
					$trenutni = $unosi_funkcije[$i];
					$prethodni = $unosi_funkcije[$i+1]; //sortirani su unazad

					$funkcija_menjano = "";

					if($trenutni['pfunkcija']!=$prethodni['pfunkcija']  ){
						//ima i mesto funkcije
						//|| 	$trenutni['pfm']!=$prethodni['pfm']

						$funkcija_menjano = " promena_funkcije = 1 ";
					}

					if(empty($funkcija_menjano))
						continue;

					$upit_update  = 
						"UPDATE `promene` SET ". $funkcija_menjano
						." WHERE `pid` = {$trenutni['pid']};"; 

					$result = $conn->query($upit_update);

						print_r('<pre>');
						var_dump($result);
						print_r('</pre>');
						

				}
		    }
		} else {
		    echo "0 results";
		}


	}


	function promena_pfm($conn='')
	{
		//sta se njima update-uje uopste?
		//oni koji menjali stranku, ali su promenili funkciju, opstinu, vlast
		// bas me briga da li su vec obradjeni kroz preletace, odradi ih sada ponovo

		$upit = 'SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as promena_funkcija from ( SELECT posoba, 1 as broj, popstina FROM promene group by posoba, pfunkcija ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid group by posoba having promena_funkcija > 1 ORDER BY `promena_funkcija` DESC';


		$result = $conn->query($upit);



		if ($result->num_rows > 0) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		       $result1 = $conn->query("SELECT * FROM `promene` WHERE posoba = ".$row['id'] ." ORDER by pod DESC");
		       $unosi_funkcije = $result1->fetch_all(MYSQLI_ASSOC);
		       

		       //prvi ne gledam, za njega ne unosim nikakve promene
				for ($i=0, $duz = count($unosi_funkcije) -1 ; $i < $duz ; $i++) { 
					
					$trenutni = $unosi_funkcije[$i];
					$prethodni = $unosi_funkcije[$i+1]; //sortirani su unazad

					$funkcija_menjano = "";

					if($trenutni['pfunkcija']!=$prethodni['pfunkcija']  ){
						//ima i mesto funkcije
						//|| 	$trenutni['pfm']!=$prethodni['pfm']

						$funkcija_menjano = " promena_funkcije = 1 ";
					}

					if(empty($funkcija_menjano))
						continue;

					$upit_update  = 
						"UPDATE `promene` SET ". $funkcija_menjano
						." WHERE `pid` = {$trenutni['pid']};"; 

					$result = $conn->query($upit_update);

						print_r('<pre>');
						var_dump($result);
						print_r('</pre>');
						

				}
		    }
		} else {
		    echo "0 results";
		}


	}
//trebalo bii pfm i promena opstine



?>