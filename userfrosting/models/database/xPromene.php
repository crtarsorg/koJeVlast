<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xPromene Class
 *
 * CRU za promene
 *
 */

class xPromene extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "promene";


    public function lista($app){

        $conn = Capsule::connection();
        $res = $conn->table('promene')->join('akteri', 'posoba', '=', 'aid')->join('stranke', 'pstranka', '=', 'sid')->join('funkcije', 'pfunkcija', '=', 'fid')->join('koalicije', 'pkoalicija', '=', 'kid')->join('opstine', 'popstina', '=', 'opid')->orderBy("pid","desc")->get();
        $dump="";

        for($i=0;$i<count($res);$i++){
            if($res[$i]['pnavlasti']){$res[$i]['pnavlasti']="Na vlasti";}else{$res[$i]['pnavlasti']="Nije na vlasti";}
        }

        //$dump= print_r($res,true);

        $app->render('promene.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "promene" => $res
        ]);


    }

    // list akter fields
    public function editPromene($app,$pid){

        $conn = Capsule::connection();
        $res = $conn->table('promene')->join('akteri', 'posoba', '=', 'aid')->join('stranke', 'pstranka', '=', 'sid')->join('funkcije', 'pfunkcija', '=', 'fid')->join('koalicije', 'pkoalicija', '=', 'kid')->join('opstine', 'popstina', '=', 'opid')->where('pid', '=', $pid)->get();


        $dump= "edit for no: ".$pid;
        //$dump= print_r($res,true);

        //$dump.= print_r($resakt,true);

        //akter
        $resakt = $conn->table('akteri')->orderBy('aprezime','asc')->get();
        $akter = '<select id="akter" name="akter"><option value=""></option>';
        foreach($resakt as $resa ){
            if($resa['aid']==$res[0]['posoba']) {$sela = "selected";}else{$sela = "";}
            $akter .= '
            <option '.$sela.' value="'.$resa['aid'].'">'.$resa['aprezime'].' '.$resa['aime'].'</option>';
        }
        $akter .= '</select>';

        //stranka
        $resstranka = $conn->table('stranke')->orderBy('snaziv','asc')->get();
        $stranka = '<select id="stranka" name="stranka"><option value=""></option>';
        foreach($resstranka as $resstrankaa ){
            if($resstrankaa['sid']==$res[0]['pstranka']) {$sela = "selected";}else{$sela = "";}
            $stranka .= '
            <option '.$sela.' value="'.$resstrankaa['sid'].'">'.$resstrankaa['snaziv'].'</option>';
        }
        $stranka .= '</select>';


        //funkcija
        $resf = $conn->table('funkcije')->orderBy('funkcija','asc')->get();
        $funk = '<select id="funk" name="funk"><option value=""></option>';
        foreach($resf as $resfa ){
            if($resfa['fid']==$res[0]['pfunkcija']) {$sela = "selected";}else{$sela = "";}
            $funk .= '
            <option '.$sela.' value="'.$resfa['fid'].'">'.$resfa['funkcija'].'</option>';
        }
        $funk .= '</select>';


        //koalicija
        $resk = $conn->table('koalicije')->orderBy('knaziv','asc')->get();
        $koal = '<select id="koalicija" name="koalicija"><option value=""></option>';
        foreach($resk as $reska ){
            if($reska['kid']==$res[0]['pkoalicija']) {$sela = "selected";}else{$sela = "";}
            $koal .= '
            <option '.$sela.' value="'.$reska['kid'].'">'.$reska['knaziv'].'</option>';
        }
        $koal .= '</select>';


        //opstina
        $reso = $conn->table('opstine')->orderBy('opstina','asc')->get();
        $ops = '<select id="opstina" name="opstina"><option value=""></option>';
        foreach($reso as $resoa ){
            if($resoa['opid']==$res[0]['popstina']) {$sela = "selected";}else{$sela = "";}
            $ops .= '
            <option '.$sela.' value="'.$resoa['opid'].'">'.$resoa['opstina'].'</option>';
        }
        $ops .= '</select>';

        //na vlasti
        $vlast = '<select id="vlast" name="vlast"><option value=""></option>';

        $seltrue ='';$selfalse = '';
        if($res[0]['pnavlasti']){$seltrue = 'selected';}  else {$selfalse = 'selected';}

        $vlast .= '<option '.$seltrue.' value="1">Na vlasti</option>';
        $vlast .= '<option '.$selfalse.' value="0">Nije na vlasti</option>';
        $vlast .= '</select>';

        $pod = $res[0]['pod'];
        $pdo = $res[0]['pdo'];



        $app->render('promeneEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "akter" => $akter,
            "stranka" => $stranka,
            "funk" => $funk,
            "koal" => $koal,
            "ops" => $ops,
            "vlast" => $vlast,
            "pod" => $pod,
            "pdo" => $pdo,
            "promene" => $res
        ]);

    }



    // update aktera - process POST
    public function editPromenePost($app,$pid){
        $dump="";
//$dump .= "\r\n".print_r($_POST,true);
//echo $dump;

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/promene-update.json");
        // Get the alert message stream
        $ms = $app->alerts;
        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            // MUST USE $app->alerts->getAndClearMessages()  to clear piled errors
            $app->render('alerts.twig', [
                "paginate_server_side" => false,
                "dump" => $dump,
                "alerts" => $app->alerts->getAndClearMessages()
            ]);
            die(); //$app->halt(400);

        }

////check if post same as existing data - prevent 0 on update
$restest = $conn->table('promene')->where('pid', '=', $pid)->get();
if( $restest[0]['posoba'] == $_POST['akter'] && $restest[0]['pstranka'] == $_POST['stranka'] && $restest[0]['pfunkcija'] == $_POST['funk'] && $restest[0]['pkoalicija'] == $_POST['koalicija'] && $restest[0]['popstina'] == $_POST['opstina'] && $restest[0]['pnavlasti'] == $_POST['vlast'] && $restest[0]['pod'] == $_POST['altdatumod'] && $restest[0]['pdo'] == $_POST['altdatumdo']    )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }

if(empty($_POST['altdatumdo'])){$_POST['altdatumdo']= NULL;}

        //UPDATE TABLE DATA
        $res =  $conn->table('promene')->where('pid', '=', $pid)->update([  'posoba' => $_POST['akter'] , 'pstranka' => $_POST['stranka'] , 'pfunkcija' => $_POST['funk'] , 'pkoalicija' => $_POST['koalicija'] , 'popstina' => $_POST['opstina'] , 'pnavlasti' => $_POST['vlast'], 'pod' => $_POST['altdatumod'] , 'pdo' => $_POST['altdatumdo']   ]);


        if($res){
            die('<div class="alert alert-success">Promena primenjena.</div>');
            }else {
            die('<div class="alert alert-danger">Promena NIJE primenjena... Kontaktirajte podrsku...</div>');
            }

    }




    // Novi akter - forma
    public function addPromena($app){

        $conn = Capsule::connection();

        $dump= "Dodaj novu promenu: ";
        //$dump= print_r($res,true);
        $dummyPromene = array(array());

//Nova promena
        //akter
        $resakt = $conn->table('akteri')->orderBy('aprezime','asc')->get();
        $akter = '<select id="akter" name="akter"><option value=""></option>';
        foreach($resakt as $resa ){
            $akter .= '
            <option  value="'.$resa['aid'].'">'.$resa['aprezime'].' '.$resa['aime'].'</option>';
        }
        $akter .= '</select>';

        //stranka
        $resstranka = $conn->table('stranke')->orderBy('snaziv','asc')->get();
        $stranka = '<select id="stranka" name="stranka"><option value=""></option>';
        foreach($resstranka as $resstrankaa ){
            $stranka .= '
            <option  value="'.$resstrankaa['sid'].'">'.$resstrankaa['snaziv'].'</option>';
        }
        $stranka .= '</select>';


        //funkcija
        $resf = $conn->table('funkcije')->orderBy('funkcija','asc')->get();
        $funk = '<select id="funk" name="funk"><option value=""></option>';
        foreach($resf as $resfa ){
            $funk .= '
            <option  value="'.$resfa['fid'].'">'.$resfa['funkcija'].'</option>';
        }
        $funk .= '</select>';


        //koalicija
        $resk = $conn->table('koalicije')->orderBy('knaziv','asc')->get();
        $koal = '<select id="koalicija" name="koalicija"><option value=""></option>';
        foreach($resk as $reska ){
            $koal .= '
            <option  value="'.$reska['kid'].'">'.$reska['knaziv'].'</option>';
        }
        $koal .= '</select>';


        //opstina
        $reso = $conn->table('opstine')->orderBy('opstina','asc')->get();
        $ops = '<select id="opstina" name="opstina"><option value=""></option>';
        foreach($reso as $resoa ){
            $ops .= '
            <option  value="'.$resoa['opid'].'">'.$resoa['opstina'].'</option>';
        }
        $ops .= '</select>';

        //na vlasti
        $vlast = '<select id="vlast" name="vlast"><option value=""></option>';
        $vlast .= '<option  value="1">Na vlasti</option>';
        $vlast .= '<option  value="0">Nije na vlasti</option>';
        $vlast .= '</select>';







        $app->render('promeneEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "akter" => $akter,
            "stranka" => $stranka,
            "funk" => $funk,
            "koal" => $koal,
            "ops" => $ops,
            "vlast" => $vlast,
            "pod" => '',
            "pdo" => '',
            "promene" => $dummyPromene
        ]);

    }





    // Nova promena - process POST
    public function addPromenaPost($app){

        $conn = Capsule::connection();
        $dump="";

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/promene-update.json");


        // Get the alert message stream
        $ms = $app->alerts;
        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            // MUST USE $app->alerts->getAndClearMessages()  to clear piled errors
            $app->render('alerts.twig', [
                "paginate_server_side" => false,
                "dump" => $dump,
                "alerts" => $app->alerts->getAndClearMessages()
            ]);
            die(); //$app->halt(400);

        }

//$dump .= "\r\n".print_r($_POST,true);
//echo $dump;
//die();

if(empty($_POST['altdatumdo'])){$_POST['altdatumdo']= NULL;}

        //insert data
        $res =  $conn->table('promene')->insert([  'posoba' => $_POST['akter'] , 'pstranka' => $_POST['stranka'] , 'pfunkcija' => $_POST['funk'] , 'pkoalicija' => $_POST['koalicija'] , 'popstina' => $_POST['opstina'] , 'pnavlasti' => $_POST['vlast'], 'pod' => $_POST['altdatumod'] , 'pdo' => $_POST['altdatumdo']  ]);

        if($res){
            die('<div class="alert alert-success">Nova promena dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Nova promena NIJE dodata... Kontaktirajte podrsku.</div>');
        }


    }



}



?>