<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xKo Class
 *
 * CRU za koalicije
 *
 */


class xKo extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "koalicije";


    public function lista($app){

        $conn = Capsule::connection();
        $res = $conn->table('koalicije')->orderBy('knaziv','asc')->get();
        $dump= print_r($res,true);

        $app->render('koalicije.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "koalicije" => $res
        ]);


    }



    // Novi koalicija - forma
    public function addKoalicija($app){

        $dump= "Dodaj novu koaliciju: ";
        //$dump= print_r($res,true);

        $app->render('koalicijaAdd.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New"
        ]);

    }


    // Nova koalicija - process POST
    public function addKoalicijaPost($app){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/koalicija-add.json");

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

//proveriti da li ista opstina vec postoji u bazi - pre dodavanja
$resexist = $conn->table('koalicije')->where('knaziv', '=', $_POST['koalicija'])->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Stranka koju dodajte vec postoji u bazi.</div>');}


        //insert data
        $res =  $conn->table('koalicije')->insert([  'knaziv' => $_POST['koalicija'],'kosn' => $_POST['altdatum']  ]);

        if($res){
            die('<div class="alert alert-success">Koalicija dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Koalicija NIJE dodata... Kontaktirajte podrsku...</div>');
        }


    }


    public function editKoalicija($app,$kid){


        $conn = Capsule::connection();
        $res = $conn->table('koalicije')->where('kid', '=', $kid)->get();

        $dump= "edit for koaliciju no: ".$kid;
        //$dump= print_r($res,true);

        $app->render('koalicijaEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "koalicije" => $res
        ]);

    }

    // Novi koalicija - process POST
    public function editKoalicijaPost($app,$kid){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/koalicija-add.json");

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

//check if post same as existing data - prevent 0 on update
$restest = $conn->table('koalicije')->where('kid', '=', $kid)->get();
if( $restest[0]['knaziv'] == $_POST['koalicija'] && $restest[0]['kosn'] == $_POST['altdatum']    )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }

//proveriti da li ista opstina vec postoji u bazi - pre dodavanja
$resexist = $conn->table('koalicije')->where('knaziv', '=', $_POST['koalicija'])->where('kid', '!=', $kid)->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Koalicija koju menjate vec postoji u bazi (nova vrednost koju ste poslali vec postoji u bazi).</div></div>');}


        //insert data
        $res =  $conn->table('koalicije')->where('kid', '=', $kid)->update([  'knaziv' => $_POST['koalicija'],'kosn' => $_POST['altdatum']   ]);

        if($res){
            die('<div class="alert alert-success">Podaci za koaliciju promenjeni.</div>');
        }else {
            die('<div class="alert alert-danger">Podaci koaliciju NISU promenjeni... Kontaktirajte podrsku...</div>');
        }


    }



    public function listaStranaka($app){

        $conn = Capsule::connection();
        $res = $conn->table('stranke')->orderBy('snaziv','asc')->get();
        $dump= print_r($res,true);

        foreach($res as $resout ){
            echo '<div class="stranka">'.$resout['snaziv'].' '.$resout['sid'].'
            <span><button type="button" class="btn btn-block btn-md btn-primary " data-id="'.$resout['sid'].'" ><i class="fa fa-trash-o"></i>Dodaj</button></span>
            <div class="clear"></div>
            </div>';
        }
        die();
        //die($dump);

    }


    public function listaStranakaUKoaliciji($app,$kid){

        $conn = Capsule::connection();
        $res = $conn->table('kstranke')->join('stranke', 'sid', '=', 'stranka')->where('koalicija','=',$kid)->get();             //ksid koalicija stranka
        $dump= print_r($res,true);

        foreach($res as $resout ){
            echo '<div class="stranka">'.$resout['snaziv'].' '.$resout['stranka'].'
            <span><button type="button" class="btn btn-block btn-md btn-danger " data-ksid="'.$resout['ksid'].'" ><i class="fa fa-trash-o"></i>Ukloni</button></span>
            <div class="clear"></div>
            </div>';
        }
        die();
        //die($dump);

    }

    public function ukloniStrankiIzKoalicije($app,$ksid){
// TODO - dodatna provera za add remove stranaka u koalicije ???
        $conn = Capsule::connection();
        $res = $conn->table('kstranke')->where('ksid', '=', $ksid)->delete();

        if($res){
            die('<div class="alert alert-success">Stranka uklonjena iz koalicije.</div>');
        }else {
            die('<div class="alert alert-danger">Stranka nije uklonjena iz koalicije... Kontaktirajte podrsku...</div>');
        }

        //die($ksid);
        //die($dump);

    }

    public function dodajStrankuUKoaliciju($app,$kid,$sid){
// TODO - dodatna provera za add remove stranaka u koalicije ???
        $conn = Capsule::connection();

        //check if alreadu exist
        $rescheck = $conn->table('kstranke')->where('koalicija', '=', $kid)->where('stranka', '=', $sid)->get();
        if(count($rescheck)>0){die('<div class="alert alert-danger">Stranka koju dodajte vec postoji u koaliciji.</div></div>');}

        //do the insert
        $res =  $conn->table('kstranke')->insert([  'koalicija' => $kid,'stranka' => $sid  ]);

        if($res){
            die('<div class="alert alert-success">Stranka dodata u koaliciju.</div>');
        }else {
            die('<div class="alert alert-danger">Stranka NIJE dodata u koaliciju... Kontaktirajte podrsku...</div>');
        }

        //die($kid." u ".$sid);
        //die($dump);

    }






}



?>