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

    ///api/akteri - svi AKTIVNI akteri  i njihova poslednja promena
    public function sviAkteri($app){
        $conn = Capsule::connection();
        //$res = $conn->table('promene')->select("aid","aime","aprezime")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->orderBy('pod','desc')->limit(5)->get();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->
        leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->
        leftJoin('opstine', 'popstina', '=', 'opid')

        //DISABLED BY REQUEST 24.08.2017 - revert to show all promene
        //samo aktivni
        //SELECT * FROM `promene` WHERE  (pdo IS NULL OR pdo>now()) AND pfunkcija not in (5,7,8,9) GROUP by posoba
//        ->whereNotIn('pfunkcija', array('5','7','8','9'))
//        ->where(function ($query) {
//                                $query->whereNull('pdo');
//                                $query->orwhere('pdo', '>',  @date('Y-m-d') ) ;
//                })
//
//        ->groupBy('posoba')
        //nastavak za listu svih promena

        ->orderBy('pod','desc')->limit(0)
        ->get();
        //->toSql();

//echo "<pre>";
//var_dump($res);
//echo "</pre>";

        $dataout = array();
        foreach($res as $key=>$val){

            if($val['pnavlasti']==1) {$navlasti = "Vlast";} else if($val['pnavlasti']==2){$navlasti = "Opozicija";} else {$navlasti = "";}
            //proveri da li je validan datum i serviraj
            if($val['pod'] == @date('Y-m-d',strtotime($val['pod']))) { $datumod = @date('d.m.Y',strtotime($val['pod']))  ; } else { $datumod ="";}
            if($val['pdo'] == @date('Y-m-d',strtotime($val['pdo']))) { $datumdo = @date('d.m.Y',strtotime($val['pdo']))  ; } else { $datumdo ="";}


            $dataout['data'][] = array($val['aid'],$val['aime'],$val['aprezime'],$val['snaziv'],$val['funkcija'],$val['knaziv'],$val['opstina'],$datumod,$datumdo,$navlasti);
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
        $res = $conn->table('promene')->select("pid","pnavlasti","pod","pdo","aime","aprezime","apol","arodjen","snaziv","fid","funkcija","kid","knaziv","opstina","okrug","ograd")
        ->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')
        ->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')
        ->leftJoin('opstine', 'popstina', '=', 'opid')
        ->where('oidokruga', '=', $oid)

        //subquery to select MAX pid - latest change - in GROUP BY
        /*->whereraw('pid IN (SELECT max(pid) FROM promene GROUP BY posoba)')*/

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
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('posoba', '=', $aid)->orderBy('pfunkcija','desc')->orderBy('pod','desc')->get();

        echo json_encode($res);
    }

    //  api/opstine
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

    //  api/strankaNaVlasti
    public function strankeNaVlastiPoOpstinama($app){

    $this->checkCache("strankeNaVlastiPoOpstinama");

        $conn = Capsule::connection();
        //$res = $conn->table('promene')->select("popstina","snaziv","pnavlasti","opstina","oidopstine","oidokruga","sid")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->groupby("popstina","pnavlasti","pstranka")->toSql();
        $res = $conn->select($conn->raw(' select count(DISTINCT posoba) AS odbornika,`popstina`,`popstina` as id, oidopstine as idopstine, oidokruga as idokruga,  case when `snaziv` IS null then "Nepoznata" else `snaziv` end as `snaziv`, `pnavlasti`, `opstina`, `oidopstine`, `oidokruga`, `sid` from `promene` left join `akteri` on `posoba` = `aid` left join `stranke` on `pstranka` = `sid` left join `funkcije` on `pfunkcija` = `fid` left join `koalicije` on `pkoalicija` = `kid` left join `funkcije_mesto` on `pfm` = `fmid` left join `opstine` on `popstina` = `opid`  where (posoba, pfunkcija,  pnavlasti) in (select posoba, pfunkcija , pnavlasti from osoba_pocetak_kraj_1 ) and (pdo is null or pdo > now())  group by `popstina`, `pnavlasti`, `pstranka` '));


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
    $this->createCache("strankeNaVlastiPoOpstinama",json_encode($out));

    }

    //  api/imajuPromene
    public function akteriKojImajuPromene($app){

        $conn = Capsule::connection();
        $res = $conn->select($conn->raw('SELECT posoba AS id, count(posoba) AS broj FROM promene GROUP BY posoba HAVING broj>1 ORDER BY broj DESC'));

        echo json_encode($res);


    }


    //dodat i vrbas
    public function preletaci($app)
    {
        //where pstranka not in (54)
        $conn = Capsule::connection();

        $sql = 'SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as prelet, posebni from ( SELECT posoba, COUNT( DISTINCT pstranka) as broj, CASE WHEN pod < "2017-05-19" and popstina in ( 195, 115 ) THEN "jesu" WHEN pod < "2017-02-14" and popstina in ( 189 )  THEN "jesu"  ELSE "nisu" END AS posebni , popstina FROM promene group by posoba, pstranka ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid WHERE posebni="nisu" group by posoba having prelet > 1 ORDER BY `prelet` DESC';

        /*
        'SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as prelet from ( SELECT posoba, COUNT( DISTINCT pstranka) as broj, popstina FROM promene   group by posoba, pstranka ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid  group by posoba having prelet > 1 ORDER BY `prelet` DESC'
         */

        $res = $conn->select($conn->raw( $sql ));

        echo json_encode($res);
    }

    public function preletaci_po_opstinama($value='')
    {
        $sql = 'SELECT opstina, count(opstina) as brPreletaca, lat, lng, GROUP_CONCAT(DISTINCT ime SEPARATOR  ",<br> ") as Osoba   from ( SELECT posoba as id, concat( "<a href=\"http://kojenavlasti.rs/promeneAkter.php?id=",aid,"\" target=\"_blank\" >", aime, " ", aprezime,"</a>") as ime, opstina, count(broj) as prelet, lat, lng, posebni FROM ( SELECT posoba, COUNT( DISTINCT pstranka) as broj, CASE WHEN pod < "2017-05-19" and popstina in ( 195, 115 ) THEN "jesu"  WHEN pod < "2017-02-14" and popstina in ( 189 )  THEN "jesu"  ELSE "nisu" END AS posebni , popstina FROM promene group by posoba, pstranka ) la LEFT JOIN akteri on la.posoba =akteri.aid LEFT JOIN opstine on la.popstina = opstine.opid WHERE posebni="nisu" group by posoba having prelet > 1 ORDER BY `prelet` DESC ) t1 GROUP by opstina ORDER BY `brPreletaca` DESC';

        /*'SELECT opstina, count(opstina) as brPreletaca, lat, lng  from ( SELECT posoba as id, concat( aime, " ", aprezime) as ime, opstina, count(broj) as prelet, lat,lng from ( SELECT posoba, COUNT( DISTINCT pstranka) as broj, CASE WHEN pod > "2017-05-19" and popstina in ( 195, 115 ) THEN "jesu" ELSE "nisu" END AS posebni, popstina FROM promene group by posoba, pstranka ) la left JOIN akteri on la.posoba =akteri.aid left join opstine on la.popstina = opstine.opid group by posoba having prelet > 1 ORDER BY `prelet` DESC) t1 GROUP by opstina ORDER BY `brPreletaca` DESC';
*/
        $conn = Capsule::connection();
        $res = $conn->select($conn->raw( $sql ));

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
/*'SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid WHERE (pdo is NULL or pdo> NOW() ) and (posoba, pfunkcija, pnavlasti) in (select posoba, pfunkcija, pnavlasti from osoba_pocetak_kraj_1 ) ORDER BY pod desc, pid desc) x  where pfunkcija not in (5,7,8,9) GROUP BY posoba';*/


    //mora da ima ista polja kao sto je imao i tamo

        $upit =
        'SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid WHERE (pdo is NULL or pdo> NOW() ) and (posoba, pfunkcija, pnavlasti) in (select posoba, pfunkcija, pnavlasti from osoba_pocetak_kraj_1 ) ORDER BY pod desc, pid desc) x  where pfunkcija not in (5,7,8,9) GROUP BY posoba';
       /* "SELECT * FROM `promene` LEFT JOIN stranke ON pstranka=sid WHERE (pdo IS NULL OR pdo>now()) AND pfunkcija not in (5,7,8,9) GROUP by posoba";*/

        $res = $conn->select($conn->raw( $upit ));

        $broj_op = $conn->select($conn->raw( "select COUNT(DISTINCT popstina) as broj from (". $upit." ) la" ));
        $stats['data']['opstina'] = $broj_op[0]['broj'];

        $stats['data']['akteri_aktivni'] = count($res);
        //set vals to 0
        $stats['data']['akteri_aktivni_vlast'] = 0;
        $stats['data']['akteri_aktivni_opozicija'] = 0;
        $stats['data']['akteri_aktivni_bez_statusa'] = 0;

        $stats['data']['saStranka'] = 0;
        $stats['data']['bezStranka'] = 0;

        $sa_stranka = array_filter($res, function ($el)
        {
            return $el['pstranka'] !=14;   
        });

        //uraditi oduzimanje: ukupni - oni_sa_strankama

        $bez_stranka = array_filter($res, function ($el)
        {
            return $el['pstranka'] ==14;   
        });


        $stats['data']['saStranka'] = count($sa_stranka);
        $stats['data']['bezStranka'] = count($bez_stranka);


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

//userfrosting/models/database/xApi.php on line 337: array_count_values() expects parameter 1 to be array, null given
//        $aabss = array_count_values($temp['akteri_aktivni_bez_statusa_stranka']);
//        arsort($aabss,SORT_NUMERIC );
//        $stats['data']['akteri_aktivni_bez_statusa_stranka'] = $aabss ;

/*'Select COUNT(*) broj, apol from (SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid left join akteri on aid=posoba WHERE (pdo is NULL or pdo> NOW() ) and  (posoba, pfunkcija, pnavlasti) in (select posoba, pfunkcija, pnavlasti from osoba_pocetak_kraj_1 ) ORDER BY pod desc, pid desc) x where pfunkcija not in (5,7,8,9) GROUP BY posoba) la group by apol ORDER BY `la`.`apol` ASC'*/

        $upit_pol = /*'Select COUNT(*) broj, apol from (SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid left join akteri on aid=posoba WHERE (pdo is NULL or pdo> NOW() ) and  (posoba, pfunkcija, pnavlasti) in (select posoba, pfunkcija, pnavlasti from osoba_pocetak_kraj_1 ) ORDER BY pod desc, pid desc) x where pfunkcija not in (5,7,8,9) GROUP BY posoba) la group by apol ORDER BY `la`.`apol` ASC';*/
        'Select COUNT(*) broj, apol from (SELECT * FROM (SELECT * FROM promene LEFT JOIN stranke ON pstranka=sid left join akteri on aid=posoba WHERE (pdo is NULL or pdo> NOW() )  ORDER BY pod desc, pid desc) x where pfunkcija not in (5,7,8,9) GROUP BY posoba) la group by apol ORDER BY `la`.`apol` ASC';
        $res_pol = $conn->select($conn->raw( $upit_pol ));


        //muskraci
        /*$res = $conn->table('promene')->select("pid")->leftJoin('akteri', 'posoba', '=', 'aid')->where('apol', '=', 'M')->groupBy('posoba')->get();*/
        $stats['data']['muskaraca'] = 0;
        $stats['data']['zena'] = 0;
        foreach ($res_pol as  $value) {
            if($value['apol'] === "M"){
                $stats['data']['muskaraca'] = $value['broj'];
            }
            else  if($value['apol'] === "Z"){
                $stats['data']['zena'] = $value['broj'];
            }
        }
         //ubaciti proveru da li je [0][apol]=="M"

        //zene
        /*$res = $conn->table('promene')->select("pid")->leftJoin('akteri', 'posoba', '=', 'aid')->where('apol', '=', 'Z')->groupBy('posoba')->get();*/


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
            $this->sendEmail("Korisnik je poslao novi predlog promene na sajtu kojenavlasti.rs");
            die('<div class="alert alert-success">Uspešno ste prosledili zahtev za promenu podataka.</div>');
        }else {
            die('<div class="alert alert-danger">Zahtev NIJE prosledjen... Doslo je do greske.</div>');
        }


    }


    // lista aktera po opstinama ime prez zanimanje rodjen + STRANKA + FUNKCIJA    - POTREBNO DORADITI SA POSLEDNJIM PODACIMA
    //  api/akteriPoOpstini/:id
    public function akteriPoOpstini($app,$id){
        $conn = Capsule::connection();

        // Mihajlov view - ne obuhvata sve odbornike
        //$res = $conn->select($conn->raw('SELECT * FROM (SELECT * FROM promene_detalji WHERE (pdo is NULL or pdo >NOW() ) and popstina = '.$id.' and (posoba, pfunkcija, pnavlasti)  in (SELECT posoba, pfunkcija, pnavlasti FROM promene where popstina = '.$id.' group by posoba,pod, pfunkcija HAVING count(*) = 1) group by posoba,pod, fid ORDER BY posoba desc, pid desc) x GROUP BY posoba'));

        //priveremeni resore stare funkcionalnosti dok ne resimo odbornike koji nedostaju
        $res = $conn->table('promene')
             ->select()
             ->leftJoin('akteri', 'posoba', '=', 'aid')
             ->leftJoin('stranke', 'pstranka', '=', 'sid')
             ->leftJoin('funkcije', 'pfunkcija', '=', 'fid')
             ->leftJoin('koalicije', 'pkoalicija', '=', 'kid')
             ->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')
             ->leftJoin('opstine', 'popstina', '=', 'opid')
             ->where('popstina', '=', $id)
             ->where(function($query){
                    return $query
                        ->whereNull('pdo')
                        ->orWhere('pdo', '>', 'now()');
             })
            ->groupby("posoba"/*,'pod','pfunkcija'*/)
            //->havingRaw('count(*) = 1)')
            ->orderBy("posoba","desc")
            ->orderBy("pid", "desc");
            
        //var_dump($res->toSql());

        $res = $res->get();
        
        
        //DB::enableQueryLog();
       /* $conn->enableQueryLog();

        
        var_dump($conn->getQueryLog());*/

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
            $out[$val['posoba']]['fid']= $val['pfunkcija'];
            $out[$val['posoba']]['stranka']= $val['snaziv'];
            $out[$val['posoba']]['vlast']= $val['pnavlasti'];
            $out[$val['posoba']]['koalicija']= $val['knaziv'];
            $out[$val['posoba']]['datum']= $val['pod'];



        }

        $out = array_values($out);
        echo json_encode($out);

    }



    public function regionInfo($app,$regid){


        //echo $regid;
        if(!is_numeric($regid)) {die("Only region ID allowed!!!");}

        $conn = Capsule::connection();
        $res = $conn->select($conn->raw(' SELECT sum(opov) AS opov, sum(opop) AS opop, okrug AS Okrug, oidokruga as id FROM `opstine` WHERE oidokruga='.$regid.' group by oidokruga  '));
        echo json_encode($res);

        //die($regid);
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


public function sendEmail($subject){

        $sendTo = "office@crta.rs";
        //$subject = $subject;
        $from = "info@kojenavlati.rs";
        $reply = "info@kojenavlati.rs";
        $headers = "From: " . "<" . $from .">\r\n";
        $headers .= "Reply-To: " . $reply . "\r\n";
        $headers .= "Content-Type: text/html;charset=utf-8\r\n";
        $headers .= "Return-path: " . $reply;
        $poruka = 	"";

        @mail($sendTo, $subject, $poruka, $headers, "-f{$from}");

//$r = mail($to, $subject, $message, $headers, '-fwebmaster@example.com');

}





}



/* broj odbornika po opstinama

select popstina, o.opstina, count(*) brojOdbornika from (select * FROM `promene` WHERE (pdo IS NULL OR pdo>now()) AND pfunkcija not in (5,7,8,9) group by posoba,popstina ) t INNER JOIN opstine o on o.opid=popstina group by t.popstina
 */

?>