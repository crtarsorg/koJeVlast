<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xApi Class
 *
 *
 *
 */
    //Connection.php  / Binding, Operators, paginate, insertGetId -> Builder.php
    //custom SQL
    //$sql = "SELECT aid, aime FROM akteri  ";
    //$res = $conn->select($sql);
    //$res = $conn->selectOne($sql);
//$res = $conn->table('akteri')->where('aid', '<', 3)->get();
//$res = $conn->table('akteri')->where('aid', '=', 3)->get();                                     // vraca row
//$res = $conn->table('akteri')->where('aid', '<', 3)->get();                                     // vraca row
//$res = $conn->table('funkcije')->where('fid', '=', 3)->delete();                                // vraca 0/1
//$res = $conn->select('select * from akteri where aid = ?', array(1));
//$res =  $conn->table('funkcije')->where('fid', '=', 1)->update(['funkcija' => "test22"]);    // vraca 0/1
    //$dump= print_r($res,true);
    //$akteri = [["aime"=>"1 wqqw"],["aime"=>"2 wqqw"],["aime"=>"3 wqqw"]];

class xApi extends UFModel {

    protected static $_table_id = "promene";

    public function sviAkteri($app){
        $conn = Capsule::connection();
        //$res = $conn->table('promene')->select("aid","aime","aprezime")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->orderBy('pod','desc')->limit(5)->get();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->
        leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->
        leftJoin('opstine', 'popstina', '=', 'opid')
        ->orderBy('pod','desc')->limit(0)
        ->get();

//echo "<pre>";
//var_dump($res);
//echo "</pre>";

        $dataout = array();
        foreach($res as $key=>$val){
            $dataout['data'][] = array($val[aid],$val[aime],$val[aprezime],$val[snaziv],$val[funkcija],$val[knaziv],$val[opstina],$val[pod],$val[pdo],$val[pnavlasti]);
        }

        echo json_encode($dataout);
    }


    //lista groupby aktera po regionima
    public function sviAkteriPoRegionima($app,$oid){


    $this->checkCache("sviAkteriPoRegionima".$oid);

        $conn = Capsule::connection();

    //$resmax = $conn->table('promene')->selectraw( 'max(pid) ')->groupBy('posoba')->get();
    //die();

        //$res = $conn->table('promene')->select("aid","aime","aprezime")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->orderBy('pod','desc')->limit(5)->get();
        $res = $conn->table('promene')->select("pid","pnavlasti","pod","pdo","aime","aprezime","apol","arodjen","snaziv","funkcija","kid","knaziv","opstina","okrug","ograd")
        ->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')
        ->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')
        ->leftJoin('opstine', 'popstina', '=', 'opid')
        ->where('oidokruga', '=', $oid)

        //subquery to select MAX pid - latest change - in GROUP BY
        ->whereraw('pid IN (SELECT max(pid) FROM promene GROUP BY posoba)')

        ->where(function ($query) {
                                $query->whereNull('pdo');
                                $query->orwhere('pdo', '>',  @date('Y-m-d') ) ;
                })

        ->groupBy('posoba')
        ->orderBy('pod','desc')->limit(0)
        ->get();
        //->toSql();

        //echo($res);

//echo "<pre>";
//var_dump($res);
//echo "</pre>";

//        $dataout = array();
//        foreach($res as $key=>$val){
//            $dataout['data'][] = array($val[aid],'',$val[aime],$val[aprezime],$val[snaziv],$val[funkcija],$val[knaziv],$val[opstina],$val[pod],$val[pdo],$val[pnavlasti],'');
//        }
//
//        echo json_encode($dataout);
        //echo json_encode($res);
        $this->createCache("sviAkteriPoRegionima".$oid,json_encode($res));
    }



    public function akterPromene($app,$aid){
        $conn = Capsule::connection();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('posoba', '=', $aid)->orderBy('pod','desc')->get();

        echo json_encode($res);
    }

    public function listaOpstina($app){
        $conn = Capsule::connection();
        $res = $conn->table('opstine')->get();

        echo json_encode($res);
    }

    public function listaStranaka($app){
        $conn = Capsule::connection();
        $res = $conn->table('stranke')->select("sid","snaziv AS naziv_stranke")->get();

        echo json_encode($res);
    }

    public function strankaNaVlastiUOpstini($app,$id){
        $conn = Capsule::connection();
        $res = $conn->table('promene')->select("snaziv","pnavlasti")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('popstina', '=', $id)->groupby("pnavlasti","pstranka")->get();

        //prepare result
        $out = array();
        foreach ($res as $val) {
            //echo print_r($val,true)."<br>";
            if($val['pnavlasti']==1){$out['vlast'][]= $val['snaziv']; }
            if($val['pnavlasti']==2){$out['opozicija'][]= $val['snaziv']; }
        }
        echo json_encode($out);

    }

    public function strankeNaVlastiPoOpstinama($app){
        $conn = Capsule::connection();
        //$res = $conn->table('promene')->select("popstina","snaziv","pnavlasti","opstina","oidopstine","oidokruga","sid")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->groupby("popstina","pnavlasti","pstranka")->toSql();
        $res = $conn->select($conn->raw(' select count(DISTINCT posoba) AS odbornika,`popstina`,  case when `snaziv` IS null then "Nepoznata" else `snaziv` end as `snaziv`, `pnavlasti`, `opstina`, `oidopstine`, `oidokruga`, `sid` from `promene` left join `akteri` on `posoba` = `aid` left join `stranke` on `pstranka` = `sid` left join `funkcije` on `pfunkcija` = `fid` left join `koalicije` on `pkoalicija` = `kid` left join `funkcije_mesto` on `pfm` = `fmid` left join `opstine` on `popstina` = `opid` where posoba not in (SELECT * from osobe_pocetak_kraj) group by `popstina`, `pnavlasti`, `pstranka` '));


//echo "<pre>";
//var_dump($res);
//echo "</pre>";
//die();

        //prepare result
        $out = array();
        foreach ($res as $val) {
            //echo print_r($val,true)."<br>";

            if($val['popstina']){
                $out[$val['popstina']]['id'] = $val['popstina'] ;
                $out[$val['popstina']]['opstina'] = $val['opstina'] ;
                $out[$val['popstina']]['idopstine'] = $val['oidopstine'] ;
                $out[$val['popstina']]['idokruga'] = $val['oidokruga'] ;

                if($val['pnavlasti']==1){
                    $out[$val['popstina']]['vlast'][]= array("id"=>$val['sid'],"naziv"=>$val['snaziv'],"odbornika"=>$val['odbornika']);

                    }
                if($val['pnavlasti']==2){
                    $out[$val['popstina']]['opozicija'][]= array("id"=>$val['sid'],"naziv"=>$val['snaziv'],"odbornika"=>$val['odbornika']);

                    }
            }
        }
        $out = array_values($out);
        echo json_encode($out);

    }


    public function akteriKojImajuPromene($app){

        $conn = Capsule::connection();
        $res = $conn->select($conn->raw('SELECT posoba AS id, count(posoba) AS broj FROM promene GROUP BY posoba HAVING broj>1 ORDER BY broj DESC'));

        echo json_encode($res);


    }

    public function preletaci($app)
    {
        $conn = Capsule::connection();
        $res = $conn->select($conn->raw('SELECT count(broj) as prelet, posoba, akteri.* from ( SELECT posoba, COUNT( DISTINCT pstranka) as broj FROM promene group by posoba, pstranka ) la left JOIN akteri on la.posoba =akteri.aid group by posoba having prelet > 1 ORDER BY `prelet` DESC'));

        echo json_encode($res);
    }

    public function stats($app){

        $conn = Capsule::connection();
        $stats = array();

        //ukupno promena
        //$res = $conn->table('promene')->count();
        //$stats['data']['promena'] = $res;

        //ukupno aktera
        //$res = $conn->table('promene')->select('pid')->groupBy('posoba')->get();
        //$stats['data']['aktera'] = count($res);

        //$ukupno_aktera = count($res);

        //ukupno aktivnih aktera
        // vraca aktere sa maximalnim datumom PDO kod kojih je PDO NULL (jos su na funkciji) a ako ima vise pomena u istom danu vraca onu sa najvecim PID-om
        //'SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid WHERE (pdo is NULL or pdo>"'.@date('Y-m-d').'" )  ORDER BY pod desc, pid desc) x GROUP BY posoba'


        $upit = 'SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid WHERE (pdo is NULL or pdo> NOW() ) and posoba not in(select * from osobe_pocetak_kraj) ORDER BY pod desc, pid desc) x GROUP BY posoba';

        $res = $conn->select($conn->raw( $upit ));

        $broj_op = $conn->select($conn->raw( "select COUNT(DISTINCT popstina) as broj from (". $upit." ) la" ));
        $stats['data']['opstina'] = $broj_op[0]['broj'];

        $stats['data']['akteri_aktivni'] = count($res);
        //set vals to 0
        $stats['data']['akteri_aktivni_vlast'] = 0;
        $stats['data']['akteri_aktivni_opozicija'] = 0;
        $stats['data']['akteri_aktivni_bez_statusa'] = 0;


        foreach($res as $key => $val){

//echo "<pre>";
//var_dump($val);
//echo "</pre>";


            switch ($val['pnavlasti']) {
                case 1:
                    $stats['data']['akteri_aktivni_vlast']++;
                    //napuni array sa ID stranke - kasnije prebroj i zameni sa nazivom stranke
                    //$temp['akteri_aktivni_vlast_stranka'][]=$val['snaziv'];
                    //stranka not null
                    if($val['snaziv']){ $temp['akteri_aktivni_vlast_stranka'][]=$val['snaziv'];} else {$temp['akteri_aktivni_vlast_stranka'][]="Nepoznata";}  // former XX
                    break;
                case 2:
                    $stats['data']['akteri_aktivni_opozicija']++;
                    if($val['snaziv']){$temp['akteri_aktivni_opozicija_stranka'][]=$val['snaziv'];}else{$temp['akteri_aktivni_opozicija_stranka'][]="Nepoznata";}     // former XX
                    break;
                default:
                    $stats['data']['akteri_aktivni_bez_statusa']++;
                    if($val['snaziv']){$temp['akteri_aktivni_bez_statusa_stranka'][]=$val['snaziv'];}else{$temp['akteri_aktivni_bez_statusa_stranka'][]="Nepoznata";}      // former XX
            }

        }

        $aavs = array_count_values($temp['akteri_aktivni_vlast_stranka']);
        arsort($aavs,SORT_NUMERIC );
        $stats['data']['akteri_aktivni_vlast_stranka'] = $aavs ;

        $aaos = array_count_values($temp['akteri_aktivni_opozicija_stranka']);
        arsort($aaos,SORT_NUMERIC );
        $stats['data']['akteri_aktivni_opozicija_stranka'] = $aaos ;


        $aabss = array_count_values($temp['akteri_aktivni_bez_statusa_stranka']);
        arsort($aabss,SORT_NUMERIC );
        $stats['data']['akteri_aktivni_bez_statusa_stranka'] = $aabss ;



        $upit_pol = 'Select COUNT(*) broj, apol from (SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid left join akteri on aid=posoba WHERE (pdo is NULL or pdo> NOW() ) and posoba not in(select * from osobe_pocetak_kraj) ORDER BY pod desc, pid desc) x GROUP BY posoba) la group by apol ORDER BY `la`.`apol` ASC';
        $res_pol = $conn->select($conn->raw( $upit_pol ));


        //muskraci
        /*$res = $conn->table('promene')->select("pid")->leftJoin('akteri', 'posoba', '=', 'aid')->where('apol', '=', 'M')->groupBy('posoba')->get();*/
        $stats['data']['muskaraca'] = $res_pol[0]['broj']; //ubaciti proveru da li je [0][apol]=="M"

        //zene
        /*$res = $conn->table('promene')->select("pid")->leftJoin('akteri', 'posoba', '=', 'aid')->where('apol', '=', 'Z')->groupBy('posoba')->get();*/
        $stats['data']['zena'] = $res_pol[1]['broj'];

        //bez pola
        /*$res = $conn->table('promene')->select("pid")->leftJoin('akteri', 'posoba', '=', 'aid')->where('apol', '!=', 'M')->where('apol', '!=', 'Z')->groupBy('posoba')->get();
        $stats['data']['bez_pola'] = count($res);*/

        //ukupno partija
        $res = $conn->table('promene')->select('pid')->groupBy('pstranka')->get();
        $stats['data']['partija'] = count($res);

        //ukupno opstina
       /* $res = $conn->table('promene')->select('pid')->groupBy('popstina')->get();
        $stats['data']['opstina'] = count($res);*/


        //ukupno regiona
        /*$res = $conn->table('promene')->select('pid')->leftJoin('opstine', 'popstina', '=', 'opid')->groupBy('oidokruga')->get();
        $stats['data']['regiona'] = count($res);*/


        echo json_encode($stats);

//echo "<pre>";
//var_dump($res);
//echo "</pre>";
//die();


    }



    public function predlozitePromenu($app){

        //echo "forma za predlog promene";
        $app->render('predloziPromenu.twig', [

        ]);

    }


    public function predlozitePromenuPost($app){

      /*  echo "<pre>";
        var_dump($_POST);
        echo "</pre>";*/

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/predlozi-promenu.json");


        // Get the alert message stream
        $ms = $app->alerts;
        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            // MUST USE $app->alerts->getAndClearMessages()  to clear piled errors
            $app->render('alerts.twig', [
                "paginate_server_side" => false,
                "alerts" => $app->alerts->getAndClearMessages()
            ]);
            die(); //$app->halt(400);

        }


        $conn = Capsule::connection();
        //insert data
        $res =  $conn->table('zahtevi')->insert([  'zakter' => $_POST['akter'] , 'zopstina' => $_POST['opstina'] , 'zmail' => $_POST['email'] ,'zopis' => $_POST['promena'] , 'zdokaz' => $_POST['potvrda'] , 'zstatus' => "Novo"  ]);

        if($res){
            die('<div class="alert alert-success">Uspešno ste prosledili zahtev za promenu podataka.</div>');
        }else {
            die('<div class="alert alert-danger">Zahtev NIJE prosledjen... Doslo je do greske.</div>');
        }


    }


    // lista aktera po opstinama ime prez zanimanje rodjen + STRANKA + FUNKCIJA    - POTREBNO DORADITI SA POSLEDNJIM PODACIMA
    public function akteriPoOpstini($app,$id){
        $conn = Capsule::connection();


        $res = $conn->select($conn->raw('SELECT * FROM (SELECT * FROM promene_detalji WHERE (pdo is NULL or pdo >NOW() ) and popstina = '.$id.' and posoba not in (SELECT posoba FROM promene where popstina = '.$id.' group by posoba,pod, pfunkcija HAVING count(*)>1) group by posoba,pod, fid ORDER BY posoba desc, pid desc) x GROUP BY posoba'));



//echo "<pre>";
//var_dump($res);
//echo "</pre>";
        //prepare result
        $out = array();
        foreach ($res as $val) {
            //echo print_r($val,true)."<br>";
            $out[$val['posoba']]['id']= $val['posoba'];
            $out[$val['posoba']]['ime']= $val['aime'];
            $out[$val['posoba']]['prezime']= $val['aprezime'];
            $out[$val['posoba']]['pol']= $val['apol'];
            $out[$val['posoba']]['datrodj']= $val['arodjen'];
            $out[$val['posoba']]['opstina']= $val['opstina'];
            $out[$val['posoba']]['funkcija']= $val['funkcija'];
            $out[$val['posoba']]['stranka']= $val['snaziv'];
            $out[$val['posoba']]['vlast']= $val['pnavlasti'];
            $out[$val['posoba']]['koalicija']= $val['knaziv'];



        }

        $out = array_values($out);
        echo json_encode($out);

    }




    public function top5promenaOpstine($app){
        $conn = Capsule::connection();
        $res = $conn->select($conn->raw(' SELECT opstina, count(*) as brPromena FROM `promene` LEFT JOIN opstine ON popstina=opstine.opid group by popstina ORDER BY `brPromena` DESC LIMIT 5 '));

        echo json_encode($res);

}





public function removeCache($app){
    $files = glob('cache/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
}


//HELPERS
public function createCache($call, $data){
    $fp = fopen("cache/".$call.".json", 'w');
    fwrite($fp, $data);
    fclose($fp);

    echo $data;
}

public function checkCache($call){
    $flife=3600; //seconds

    if(file_exists("cache/".$call.".json")){

        clearstatcache();
        $age = time()-filemtime("cache/".$call.".json");
        if($age>$flife) {
            //go back and make query
            return;
        }else {
            //serve cached file
            echo file_get_contents("cache/".$call.".json");
            die();
        }

    } else {
        //go back and make query
        return;
    }



}






}



?>