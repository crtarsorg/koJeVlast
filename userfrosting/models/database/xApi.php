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
            $dataout['data'][] = array($val[aid],'',$val[aime],$val[aprezime],$val[snaziv],$val[funkcija],$val[knaziv],$val[opstina],$val[pod],$val[pdo],$val[pnavlasti],'');
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
        $res = $conn->table('promene')->select("popstina","snaziv","pnavlasti","opstina","oidopstine","oidokruga")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->groupby("popstina","pnavlasti","pstranka")->get();

        //prepare result
        $out = array();
        foreach ($res as $val) {
            //echo print_r($val,true)."<br>";

            if($val['popstina']){
                $out[$val['popstina']]['id'] = $val['popstina'] ;
                $out[$val['popstina']]['opstina'] = $val['opstina'] ;
                $out[$val['popstina']]['idopstine'] = $val['oidopstine'] ;
                $out[$val['popstina']]['idokruga'] = $val['oidokruga'] ;

                if($val['pnavlasti']==1){$out[$val['popstina']]['vlast'][]= $val['snaziv']; }
                if($val['pnavlasti']==2){$out[$val['popstina']]['opozicija'][]= $val['snaziv']; }
            }
        }
        $out = array_values($out);
        echo json_encode($out);

    }


    public function predlozitePromenu($app){

        //echo "forma za predlog promene";
        $app->render('predloziPromenu.twig', [

        ]);

    }


    public function predlozitePromenuPost($app){

        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";

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


    // lista aktera po opstinama ime prez zanimanje rodjen + STRANKA + FUNKCIJA
    public function akteriPoOpstini($app,$id){
        $conn = Capsule::connection();
        $res = $conn->table('promene')->select("popstina","posoba","aime","aprezime","apol","arodjen","opstina","funkcija","snaziv")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('popstina', '=', $id)->groupby("posoba")->get();

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

        }

        $out = array_values($out);
        echo json_encode($out);

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