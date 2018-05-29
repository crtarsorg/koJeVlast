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
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('opstine', 'popstina', '=', 'opid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->orderBy("pid","desc")->limit(10)->get();   //
        $dump="";


        //ovo moze u sql upitu if - if(pnavlasti=1,"Vlast","Opozicija")
        for($i=0;$i<count($res);$i++){
            if($res[$i]['pnavlasti']==1){$res[$i]['pnavlasti']="Vlast";}elseif($res[$i]['pnavlasti']==2){$res[$i]['pnavlasti']="Opozicija";}else {$res[$i]['pnavlasti']="/";}
        }

        //$dump= print_r($res,true);

        $app->render('promene.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "promene" => $res
        ]);


    }


    public function vrati_instancu($conn,$qe)
    {
        
       return $conn->table('promene')
        ->select('aime', 'aprezime','arodjen','snaziv','funkcija','fmesto','knaziv','opstina','pod','pdo','pnavlasti','pid','pkraj_mandata','promena_funkcije')
        ->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')
        ->leftJoin('funkcije', 'pfunkcija', '=', 'fid')
        ->leftJoin('koalicije', 'pkoalicija', '=', 'kid')
        ->leftJoin('opstine', 'popstina', '=', 'opid')
        ->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')

//        ->where('aime', 'like',  '' . $qe[0] .'%')
//        ->where('aprezime', 'like',  '' . $qe[1] .'%')

        ->where(function ($query) use($qe) {
                     for ($i = 0; $i < count($qe); $i++){
                        $query->orwhere('aime', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('aprezime', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('arodjen', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('snaziv', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('funkcija', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('fmesto', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('opstina', 'like',  '' . $qe[$i] .'');

                        if(is_numeric($qe[$i]) ){
                        $query->orwhere('pod', 'like',  '' . $qe[$i] .'');
                        $query->orwhere('pdo', 'like',  '' . $qe[$i] .'');
                        }
                        if($qe[$i]=="0" || $qe[$i]=="1" || $qe[$i]=="2"   ){
                        $query->orwhere('pnavlasti', '=',  '' . $qe[$i] .'');
                        }
                     }
                });
    }




    public function initial_values($conn, $res="")
    {

        if(empty($res)){
            //XXX moze da se iskoristi postojeca initial_values, ako se prosledi niz praznim stringovima i odgovarajucim kljucevima 
            $res = [];
            $res[0] = [];
            $res[0]['pstranka'] = '';
            $res[0]['pfunkcija'] = '';
            $res[0]['pfm'] = '';
            $res[0]['pkoalicija'] = '';
            $res[0]['popstina'] = '';
            $res[0]['pnavlasti'] = '';
            $res[0]['pod'] = '';
            $res[0]['pdo'] = '';
            $res[0]['pkraj_mandata'] = '';
            $res[0]['promena_funkcije'] = '';
            $res[0]['tip_preleta'] = '';
            $res[0]['datum_izbora'] = '';
        }
        $ret_res = array();
      //stranka
        $resstranka = $conn->table('stranke')->orderBy('snaziv','asc')->get();
        
        $stranka = '<select id="stranka" name="stranka"><option value="0">Stranka - nema podatka</option>';
        
        foreach($resstranka as $resstrankaa ){
            if($resstrankaa['sid']==$res[0]['pstranka']) {$sela = "selected";}else{$sela = "";}
            $stranka .= '
            <option '.$sela.' value="'.$resstrankaa['sid'].'">'.$resstrankaa['snaziv'].'</option>';
        }
        $stranka .= '</select>';

        $ret_res['stranka'] = $stranka;

        //funkcija
        $resf = $conn->table('funkcije')->orderBy('funkcija','asc')->get();
        $funk = '<select id="funk" name="funk"><option value="0">Funkcija - nema podatka</option>';
        foreach($resf as $resfa ){
            if($resfa['fid']==$res[0]['pfunkcija']) {$sela = "selected";}else{$sela = "";}
            $funk .= '
            <option '.$sela.' value="'.$resfa['fid'].'">'.$resfa['funkcija'].'</option>';
        }
        $funk .= '</select>';
        $ret_res['funk'] = $funk;

        //funkcija - mesto
        $resfm = $conn->table('funkcije_mesto')->orderBy('fmesto','asc')->get();
        $funkmesto = '<select id="fmesto" name="fmesto"><option value="0">Mesto funkcije - nema podatka</option>';
        foreach($resfm as $resfma ){
            if($resfma['fmid']==$res[0]['pfm']) {$sela = "selected";}else{$sela = "";}
            $funkmesto .= '
            <option '.$sela.' value="'.$resfma['fmid'].'">'.$resfma['fmesto'].'</option>';
        }
        $funkmesto .= '</select>';
        $ret_res['funkmesto'] = $funkmesto;

        //koalicija
        $resk = $conn->table('koalicije')->orderBy('knaziv','asc')->get();
        $koal = '<select id="koalicija" name="koalicija"><option value="0">Koalicija - nema podatka</option>';
        foreach($resk as $reska ){
            if($reska['kid']==$res[0]['pkoalicija']) {$sela = "selected";}else{$sela = "";}
            $koal .= '
            <option '.$sela.' value="'.$reska['kid'].'">'.$reska['knaziv'].'</option>';
        }
        $koal .= '</select>';
        $ret_res['koal'] = $koal;


        //opstina
        $reso = $conn->table('opstine')->orderBy('opstina','asc')->get();
        $ops = '<select id="opstina" name="opstina"><option value="0">Opstina - nema podatka</option>';
        foreach($reso as $resoa ){
            if($resoa['opid']==$res[0]['popstina']) {$sela = "selected";}else{$sela = "";}
            $ops .= '
            <option '.$sela.' value="'.$resoa['opid'].'">'.$resoa['opstina'].'</option>';
        }
        $ops .= '</select>';
        $ret_res['ops'] = $ops;

        //na vlasti
        $vlast ='';$vlastsel='';$opozicija = '';$vlastdef='';
        if($res[0]['pnavlasti']==1){$vlastsel = 'selected';}  elseif($res[0]['pnavlasti']==2) {$opozicija = 'selected';} else {$vlastdef="selected";}

        $vlast = '<select id="vlast" name="vlast"><option  '.$vlastdef.'  value="0">Na vlasti - nema podatka</option>';
        $vlast .= '<option '.$vlastsel.' value="1">Vlast</option>';
        $vlast .= '<option '.$opozicija.' value="2">Opozicija</option>';
        $vlast .= '</select>';

        $ret_res['vlast'] = $vlast;


        $pod = $res[0]['pod'];
        $pdo = $res[0]['pdo'];

        $ret_res['pod'] = $pod;
        $ret_res['pdo'] = $pdo;

        $redovni = '';
        $vanredni = '';
        if($res[0]['pkraj_mandata']=='redovni'){
            $redovni = 'selected';
        }else if($res[0]['pkraj_mandata']=='vanredni'){
            $vanredni = 'selected';
        }

        $kraj_mandata = 
            '<select name="pkraj_mandata" id="mandat">'
                .'<option value=""> Nista</option>'
                .'<option '.$redovni.' value="redovni">Redovni kraj mandata</option>'
                .'<option '.$vanredni.' value="vanredni">Vanredni kraj mandata</option>'
            .'</select>';
        $ret_res['kraj_mandata'] = $kraj_mandata;

        $promena = "";
        if(!empty($res[0]['promena_funkcije']) && $res[0]['promena_funkcije'] ==1){
            $promena = "checked";    
        }
        
        $promena_funkcije = '<input type="checkbox" name="promena_funkcije" value="1" '.$promena.'> Promena funkcije';
        $ret_res['promena_funkcije'] = $promena_funkcije;



        $nezavisni = '';
        $nova_grupa = '';
        $preletanje = "";
        $preletanje_grupe = '';
        if($res[0]['tip_preleta']=='nezavisni'){
            $nezavisni = 'selected';
        }else if($res[0]['tip_preleta']=='nova_grupa'){
            $nova_grupa = 'selected';
        }else if($res[0]['tip_preleta']=='preletanje'){
            $preletanje = 'selected';
        }else if($res[0]['tip_preleta']=='preletanje_grupe'){
            $preletanje_grupe = 'selected';
        }

        $tip_preleta = 
            '<select name="tip_preleta" id="prelet">'
                .'<option value="" ></option>'
                .'<option '.$nezavisni.' value="nezavisni">Nezavisni odbornik</option>'
                .'<option '.$nova_grupa.' value="nova_grupa">Nova odbornicka grupa</option>'
                .'<option '.$preletanje.' value="preletanje">Preletanje odbornika</option>'
                .'<option '.$preletanje_grupe.' value="preletanje_grupe">Preletanje odbornicke grupe</option>'
            .'</select>';
        $ret_res['tip_preleta'] = $tip_preleta;


        $oport_prelet = "";
        if(!empty($res[0]['oport_prelet']) && $res[0]['oport_prelet'] ==1){
            $oport_prelet = "checked";    
        }
        
        $oport_prelet = '<input type="checkbox" name="oport_prelet" value="1" '.$oport_prelet.'> Oportuno preletanje';
        $ret_res['oport_prelet'] = $oport_prelet;

        $datum_izbora = '';
        if( !empty($res[0]['datum_izbora'])){
            $datum_izbora = " value='".$res[0]['datum_izbora']."'";
        }

        $datum_izb = '<input type="text" name="datum_izbora" placeholder="Datum izbora" id="datum_izbora" '.$datum_izbora.'>';

        $ret_res['datum_izbora'] = $datum_izb;
        
        

        return $ret_res;
    }





      public function listaSearch($app){

    $q = $_GET['search']['value'];
    $numres = $_GET['length'];
    $start = $_GET['start'];
    $sort = -1;//$_GET['order'][0]['column'];
    $sortorder ='desc';// $_GET['order'][0]['dir'];
    //XXX ukloniti zakucane stvari iznad

    if(empty($q)){$q='%';}
    if(!$start){$start='0';}
    if(!$sortorder){$sortorder='desc';}
    if($numres=="-1"){$numres= 999999999999;}  // $recFiltered


    $qe = explode(" ",$q);
    if(count($qe)>1){
        $qe[] = $q;//."%";
    } else{
        //$qe[] = $q."%";
    }



    switch ($sort) {
        case "0":
            $sort="aime";
            break;
        case "1":
            $sort="aprezime";;
            break;
        case "2":
            $sort="arodjen";
            break;
        case "3":
            $sort="snaziv";
            break;
        case "4":
            $sort="funkcija";
            break;
        case "5":
            $sort="fmesto";
            break;
        case "6":
            $sort="knaziv";
            break;
        case "7":
            $sort="opstina";
            break;
        case "8":
            $sort="pod";
            break;
        case "9":
            $sort="pdo";
            break;
        case "10":
            $sort="pnavlasti";
            break;
        default:
            $sort="pid";
    }

//echo($qe[0]);
//die($qe[1]);

        
        $conn = Capsule::connection();
        $recFiltered =$this->vrati_instancu($conn,$qe)
        ->count();

//prepare and fix limits

        $res = $this->vrati_instancu($conn,$qe)
        ->skip($start)->take($numres)
        ->orderBy($sort, $sortorder)
        ->get();



        $dump="";

        for($i=0;$i<count($res);$i++){
            if($res[$i]['pnavlasti']==1){$res[$i]['pnavlasti']="Vlast";}elseif($res[$i]['pnavlasti']==2){$res[$i]['pnavlasti']="Opozicija";}else {$res[$i]['pnavlasti']="/";}
            //add edit link
            $res[$i]['pid'] = '<a target="_blank" href="promene/edit/'.$res[$i]['pid'].'">edit</a> ';
        }


        //$res = array_values($res);
        for($i=0;$i<count($res);$i++){
            $res[$i] = array_values($res[$i]);
        }

        $resout['recordsFiltered']= $recFiltered;
        $resout['recordsTotal']= $conn->table('promene')->count();
        $resout['data']=$res;

        echo json_encode($resout);


    }



    // list akter fields
    public function editPromene($app,$pid){

        $conn = Capsule::connection();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('pid', '=', $pid)->get();


        $dump= "edit for no: ".$pid;

       
        $resakt = $conn->table('akteri')->orderBy('aprezime','asc')->where('aime', '=', $res[0]['aime'])->where('aprezime', '=', $res[0]['aprezime'])->get();
        
        $akter = '<select id="akter" name="akter"><option value=""></option>';
        foreach($resakt as $resa ){
            if($resa['aid']==$res[0]['posoba']) {$sela = "selected";}else{$sela = "";}
            $akter .= '
            <option '.$sela.' value="'.$resa['aid'].'">'.$resa['aprezime'].' '.$resa['aime'].' ('.$resa['arodjen'].')</option>';
        }
        $akter .= '</select>';

        
        $la = $this->initial_values($conn, $res);

        $app->render('promeneEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update (ovo NE unosi novu promenu - samo menja postojecu)",
            "akter" => $akter,
            "stranka" => $la['stranka'],
            "funk" => $la['funk'],
            "funkmesto" => $la['funkmesto'],
            "koal" => $la['koal'],
            "ops" => $la['ops'],
            "vlast" => $la['vlast'],
            "pod" => $la['pod'],
            "pdo" => $la['pdo'],
            "promene" => $res, 
            "kraj_mandata"=>$la['kraj_mandata'],
            "promena_funkcije"=>$la["promena_funkcije"],
            "tip_preleta"=>$la["tip_preleta"],
            "oport_prelet"=>$la['oport_prelet'],
            "datum_izbora"=>$la['datum_izbora'],

        ]);

    }



    public function editPromeneFORM($app,$pid){

        $conn = Capsule::connection();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('opstine', 'popstina', '=', 'opid')->where('pid', '=', $pid)->get();


        $dump= "edit for no: ".$pid;


        $akter = '<input class="form-control" id="akterdis" name="akterdis" value="'.$res[0]['aprezime'].' '.$res[0]['aime'].' '.$res[0]['aid'].'" disabled="disabled"><input id="akter" type="hidden" name="akter" value="'.$res[0]['aid'].'">';

        
        $la = $this->initial_values($conn, $res);


        $app->render('promeneEditFORM.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update (ovo NE unosi novu promenu - samo menja postojecu)",
            "akter" => $akter,
            "stranka" => $la['stranka'],
            "funk" => $la['funk'],
            "funkmesto" => $la['funkmesto'],
            "koal" => $la['koal'],
            "ops" => $la['ops'],
            "vlast" => $la['vlast'],
            "pod" => $la['pod'],
            "pdo" => $la['pdo'],
            "promene" => $res,
            "pid" => $pid,
            "kraj_mandata"=> $la['kraj_mandata'],
            "promena_funkcije"=> $la['promena_funkcije'],
            "tip_preleta"=>$la['tip_preleta'],            
            "oport_prelet"=>$la['oport_prelet'],
            "datum_izbora"=>$la['datum_izbora'],
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
if( 
    $restest[0]['posoba'] == $_POST['akter'] 
 && $restest[0]['pstranka'] == $_POST['stranka'] 
 && $restest[0]['pfunkcija'] == $_POST['funk'] 
 && $restest[0]['pfm'] == $_POST['fmesto'] 
 && $restest[0]['pkoalicija'] == $_POST['koalicija'] 
 && $restest[0]['popstina'] == $_POST['opstina'] 
 && $restest[0]['pnavlasti'] == $_POST['vlast'] 
 && $restest[0]['pod'] == $_POST['altdatumod'] 
 && $restest[0]['pdo'] == $_POST['altdatumdo']  
 && $restest[0]['pkraj_mandata'] == $_POST['pkraj_mandata']
 && (!empty($_POST['promena_funkcije']) && $restest[0]['promena_funkcije'] == $_POST['promena_funkcije'])
 && $restest[0]['tip_preleta'] == $_POST['tip_preleta']
 && $restest[0]['oport_prelet'] == $_POST['oport_prelet']
 && $restest[0]['datum_izbora'] == $_POST['altdatum_izb']    
 )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }

if(empty($_POST['altdatumdo'])){$_POST['altdatumdo']= NULL;}

        //UPDATE TABLE DATA
        $res =  $conn->table('promene')->where('pid', '=', $pid)->update([  'posoba' => $_POST['akter'] , 'pstranka' => $_POST['stranka'] , 'pfunkcija' => $_POST['funk'] ,'pfm' => $_POST['fmesto'] , 'pkoalicija' => $_POST['koalicija'] , 'popstina' => $_POST['opstina'] , 'pnavlasti' => $_POST['vlast'], 'pod' => $_POST['altdatumod'] , 'pdo' => $_POST['altdatumdo'], "pkraj_mandata"=> $_POST['pkraj_mandata'], "promena_funkcije"=> $_POST['promena_funkcije'],"tip_preleta"=> $_POST['tip_preleta'], "oport_prelet"=>$_POST['oport_prelet'], 'datum_izbora' => $_POST['altdatum_izb']  ]);


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
            <option  value="'.$resa['aid'].'">'.$resa['aprezime'].' '.$resa['aime'].' ('.$resa['arodjen'].')</option>';
        }
        $akter .= '</select>';



        $la = $this->initial_values($conn);


        $app->render('promeneEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "akter" => $akter,
            "stranka" => $la['stranka'],
            "funk" => $la['funk'],
            "funkmesto" => $la['funkmesto'],
            "koal" => $la['koal'],
            "ops" => $la['ops'],
            "vlast" => $la['vlast'],
            "pod" => '',
            "pdo" => '',
            "promene" => $dummyPromene,
            "kraj_mandata"=>$la['kraj_mandata'],
            "promena_funkcije"=>$la['promena_funkcije'],
            "tip_preleta"=>$la['tip_preleta'],
            "oport_prelet"=>$la['oport_prelet'],            
            "datum_izbora"=>$la['datum_izbora'],

        ]);

    }

    // Nova promena za korisnika - UID
    public function addPromenaZaKorisnika($app,$uid){

        $conn = Capsule::connection();

        $dump= "Dodaj novu promenu: ";
        //$dump= print_r($res,true);
        $dummyPromene = array(array());

//Nova promena
        //akter
        $resakt = $conn->table('akteri')->where('aid', '=', $uid)->get();
        $akter = '<select id="akter" name="akter"><option value=""></option>';
        foreach($resakt as $resa ){
            $akter .= '
            <option  value="'.$resa['aid'].'">'.$resa['aprezime'].' '.$resa['aime'].' ('.$resa['arodjen'].')</option>';
        }
        $akter .= '</select>';

        $akter = '<input class="form-control" id="akterdis" name="akterdis" value="'.$resakt[0]['aprezime'].' '.$resakt[0]['aime'].' '.$resakt[0]['aid'].'" disabled="disabled"><input id="akter" type="hidden" name="akter" value="'.$resakt[0]['aid'].'">';


        $la = $this->initial_values($conn);


        $app->render('promeneEditFORM.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "akter" => $akter,
            "stranka" => $la['stranka'],
            "funk" => $la['funk'],
            "funkmesto" => $la['funkmesto'],
            "koal" => $la['koal'],
            "ops" => $la['ops'],
            "vlast" => $la['vlast'],
            "pod" => '',
            "pdo" => '',
            "promene" => $dummyPromene,
            "novapromena" => '1',
            "kraj_mandata" => $la['kraj_mandata'],
            "promena_funkcije"=>$la['promena_funkcije'],
            "tip_preleta"=>$la['tip_preleta'],
            "oport_prelet"=>$la['oport_prelet'],            
            "datum_izbora"=>$la['datum_izbora'],
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
        $res =  $conn->table('promene')->insert([  'posoba' => $_POST['akter'] , 'pstranka' => $_POST['stranka'] , 'pfunkcija' => $_POST['funk'] ,'pfm' => $_POST['fmesto'] , 'pkoalicija' => $_POST['koalicija'] , 'popstina' => $_POST['opstina'] , 'pnavlasti' => $_POST['vlast'], 'pod' => $_POST['altdatumod'] , 'pdo' => $_POST['altdatumdo'], "pkraj_mandata"=>$_POST['pkraj_mandata'], "promena_funkcije"=> $_POST['promena_funkcije'],"tip_preleta"=> $_POST['tip_preleta'],"oport_prelet"=>$_POST['oport_prelet'],"datum_izbora"=>$_POST['datum_izbora'],  ]);

        if($res){
            die('<div class="alert alert-success">Nova promena dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Nova promena NIJE dodata... Kontaktirajte podrsku.</div>');
        }


    }


    //lista promena za brisanje                         - promene/brisanje
    public function listaPromenaZaBrisanje($app){

        $conn = Capsule::connection();
        $res = $conn->table('promene')->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('opstine', 'popstina', '=', 'opid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->orderBy("opstina","asc")->orderBy("aime","asc")->orderBy("aprezime","asc")->get();   //  ->limit(100)
        $dump="";

        for($i=0;$i<count($res);$i++){
            if($res[$i]['pnavlasti']==1){$res[$i]['pnavlasti']="Vlast";}elseif($res[$i]['pnavlasti']==2){$res[$i]['pnavlasti']="Opozicija";}else {$res[$i]['pnavlasti']="/";}
        }

        //$dump= print_r($res,true);

        $app->render('promeneZaBrisanje.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "promene" => $res
        ]);


    }


    public function listaPromenaZaBrisanjePromene($app,$pid){
        $dump="";
        //$dump .= "\r\n".print_r($_POST,true);
        //echo $dump;
//echo "<pre>";
//var_dump($app->alerts->getAndClearMessages());
//echo "</pre>";


//echo $pid;
        $errors = array();

        $conn = Capsule::connection();
        //uzmi sadasnje podatke
        $currdata = $conn->table('promene')->where('pid', '=', $pid)->get();

//echo "<pre>";
//var_dump($currdata);
//echo "</pre>";
//
//        die();

        if(count($currdata)<1) {$errors[]="Nije uspelo... Mozda je vec obrisan... Ozvezite stranu";}
        else {

            $resins =  $conn->table('promene_deleted')->insert([ 'pid' => $currdata[0]['pid'] ,  'posoba' => $currdata[0]['posoba'] , 'pstranka' => $currdata[0]['pstranka'] , 'pfunkcija' => $currdata[0]['pfunkcija'] ,'pfm' => $currdata[0]['pfm'] , 'pkoalicija' => $currdata[0]['pkoalicija'] , 'popstina' => $currdata[0]['popstina'] , 'pnavlasti' => $currdata[0]['pnavlasti'], 'pod' => $currdata[0]['pod'] , 'pdo' => $currdata[0]['pdo']  ]);

            //XXX ovde verovatno treba dodati i dodana polja koja su dodata naknadno

            if(!$resins){ $errors[]="GRESKA... Promena NIJE backup-ovana... Kontaktirajte podrsku."; }

            $currdatadel = $conn->table('promene')->where('pid', '=', $pid)->delete();
        }

//echo "<pre>";
//var_dump($currdatadel);
//echo "</pre>";


if(count($errors)) { echo "".implode("<br>",$errors);} else { echo "OBRISAN";}


    }




}



?>