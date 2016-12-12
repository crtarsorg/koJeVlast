<?php

$hostname_mysqlcon = "127.0.0.1";
$database_mysqlcon = "vlast";
//$database_mysqlcon = "vlastimport";
$username_mysqlcon = "root";
$password_mysqlcon = "root";

$mysqli = @new mysqli($hostname_mysqlcon, $username_mysqlcon, $password_mysqlcon,$database_mysqlcon);
@$mysqli->set_charset("utf8");



$ops = array();
$funk = array();
$stranke = array();
$koalicije = array();
$akt = array();
$fm = array();


if (($handle = fopen("odbornici.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            //echo $data[8] . "<br />\n";

            if(!empty($data[1])) {$ops[] = $data[1];}
            if(!empty($data[6])) {$funk[] = $data[6];}
            if(!empty(trim($data[9]))) {$stranke[] = trim($data[9]);}
            if(!empty(trim($data[8]))) {$koalicije[] = trim($data[8]);}
            if(!empty(trim($data[7]))) {$fm[] = trim($data[7]);}


            $date = @strtotime($data[4]);
            $newdate = @date('d.m.Y',$date);
            if($data[5]=="Musko"){$npol = "M";}else{$npol = "Z";}

            if(!empty(trim($data[2])) && !empty(trim($data[3])) ) { $akt[] = array(trim($data[2]),trim($data[3]),$npol, $newdate, $data[4]  );   }

    }
    fclose($handle);
}

$ops = array_unique($ops);
$funk = array_unique($funk);
$stranke = array_unique($stranke);
$koalicije = array_unique($koalicije);
$fm = array_unique($fm);

sort($ops);
sort($funk);
sort($stranke);
sort($koalicije);
sort($fm);

//echo "<pre>";
//var_dump($fm);
//echo "</pre>";
//die();

//truncate
$mysqli->query( "TRUNCATE opstine" ) ;
$mysqli->query( "TRUNCATE funkcije" ) ;
$mysqli->query( "TRUNCATE stranke" ) ;
$mysqli->query( "TRUNCATE koalicije" ) ;
$mysqli->query( "TRUNCATE akteri" ) ;
$mysqli->query( "TRUNCATE funkcije_mesto" ) ;
$mysqli->query( "TRUNCATE promene" ) ;


//opstine
for($i=0;$i<count($ops);$i++){
    //echo $ops[$i]."<br>";
    $sql = "INSERT INTO opstine (`opstina`) VALUES( '$ops[$i]'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".mysqli_error($mysqli)."<br>";}
}

//funkcije
for($i=0;$i<count($funk);$i++){
    $sql = "INSERT INTO funkcije (`funkcija`) VALUES( '$funk[$i]'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".mysqli_error($mysqli)."<br>";}
}

//funkcije - mesto
for($i=0;$i<count($fm);$i++){
    $sql = "INSERT INTO funkcije_mesto (`fmesto`) VALUES( '$fm[$i]'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".mysqli_error($mysqli)."<br>";}
}

//stranke
for($i=0;$i<count($stranke);$i++){
    $ins = $mysqli->real_escape_string($stranke[$i]);
    $sql = "INSERT INTO stranke (`snaziv`) VALUES( '{$ins}'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".$mysqli->error." / ".$sql."<br>";}
}

//koalicije
for($i=0;$i<count($koalicije);$i++){
    $ins = $mysqli->real_escape_string($koalicije[$i]);
    $sql = "INSERT INTO koalicije (`knaziv`) VALUES( '{$ins}'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".$mysqli->error." / ".$sql."<br>";}
}


//akteri
for($i=0;$i<count($akt);$i++){
    $ime = $mysqli->real_escape_string($akt[$i][0]);
    $prezime = $mysqli->real_escape_string($akt[$i][1]);
    $pol = $mysqli->real_escape_string($akt[$i][2]);
    $rodj =  $mysqli->real_escape_string($akt[$i][3]);


    $sql = "INSERT INTO akteri (`aime`,`aprezime`,`apol`,`arodjen`) VALUES( '{$ime}','{$prezime}','{$pol}','{$rodj}'  )"; //echo $sql."<br>";
    $res = $mysqli->query( $sql ) ;
    if($res){echo "OK<br>";}else{echo "NOT OK: ".$mysqli->error." / ".$sql."<br>";}
}


//PROMENE

if (($handle = fopen("odbornici.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $qime = trim($data[2]);
    $qprez = trim($data[3]);
    $qops = $data[1];
    $qfunk = $data[6];
    $qstranke = $mysqli->real_escape_string(trim($data[9]));
    $qkoalicije = $mysqli->real_escape_string(trim($data[8]));
    $qfm = $mysqli->real_escape_string(trim($data[7]));

    if(trim($data[11])=="Vlast"){$qvlast = "1";}elseif(trim($data[11])=="Opozicija"){$qvlast = "2";} else {$qvlast = "0";}


    if($qime && $qprez){
        $idime = $mysqli->query("SELECT aid FROM akteri WHERE aime ='{$qime}' AND aprezime ='{$qprez}'  ")->fetch_object()->aid;

        $idops = $mysqli->query("SELECT opid FROM opstine WHERE opstina ='{$qops}'   ")->fetch_object()->opid;    if(!$idops){$idops=0;}
        $idfunk = $mysqli->query("SELECT fid FROM funkcije WHERE funkcija ='{$qfunk}'   ")->fetch_object()->fid;    if(!$idfunk){$idfunk=0;}
        $idstranka = $mysqli->query("SELECT sid FROM stranke WHERE snaziv ='{$qstranke}'   ")->fetch_object()->sid;    if(!$idstranka){$idstranka=0;}
        $idkoal = $mysqli->query("SELECT kid FROM koalicije WHERE knaziv ='{$qkoalicije}'   ")->fetch_object()->kid;    if(!$idkoal){$idkoal=0;}
        $idfm = $mysqli->query("SELECT fmid FROM funkcije_mesto WHERE fmesto ='{$qfm}'   ")->fetch_object()->fmid;    if(!$idfm){$idfm=0;}

        $sqlin = "  INSERT INTO promene (`pid`, `posoba`, `pstranka`, `pfunkcija`,`pfm`, `pkoalicija`, `popstina`, `pnavlasti`, `pod`, `pdo`) VALUES (NULL, '{$idime}', '{$idstranka}', '{$idfunk}','{$idfm}', '{$idkoal}', '{$idops}', '{$qvlast}', '2016-12-01', NULL); ";
        $resin = $mysqli->query( $sqlin ) ;
    if($resin) {echo "Nova promena OK<br>";} else {echo $mysqli->error." / ".$sqlin."<br>";}


    }



    }
    fclose($handle);
}



?>