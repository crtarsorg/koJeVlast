<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xFunk Class
 *
 * CRU za funkcije
 *
 */


class xFunk extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "funkcije";


    public function listaAktera($app){

        $conn = Capsule::connection();
        $res = $conn->table('funkcije')->orderBy('funkcija','asc')->get();
        $dump="";

        $app->render('funk.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "funks" => $res
        ]);


    }

    // list akter fields
    public function editAktera($app,$fid){

        $conn = Capsule::connection();
        $res = $conn->table('funkcije')->where('fid', '=', $fid)->get();

        $dump= "edit for no: ".$fid;
        //$dump= print_r($res,true);

        $app->render('funkEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "funks" => $res
        ]);

    }

    // update aktera - process POST
    public function editAkteraPost($app,$fid){

        $conn = Capsule::connection();
        $dump="";

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/funk-update.json");
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
$restest = $conn->table('funkcije')->where('fid', '=', $fid)->get();
if( $restest[0]['funkcija'] == $_POST['funkcija']    )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }


//proveriti da li ista funkcija vec postoji u bazi - pre dodavanja
$resexist = $conn->table('funkcije')->where('funkcija', '=', $_POST['funkcija'])->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Funkcija koju menjate vec postoji u bazi (nova vrednost koju ste poslali vec postoji u bazi).</div>');}


        //UPDATE TABLE DATA
        $res =  $conn->table('funkcije')->where('fid', '=', $fid)->update([  'funkcija' => $_POST['funkcija'] ]);

        if($res){
            die('<div class="alert alert-success">Funkcija updated.</div>');
            }else {
            die('<div class="alert alert-danger">Funkcija NOT updated... Something is wrong OR NO CHANGES SENT</div>');
            }
    }


    // Novi akter - forma
    public function addFunk($app){

        $dump= "Add new function: ";
        //$dump= print_r($res,true);
        $dummyFunk = array(array());

        $app->render('funkEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "funks" => $dummyFunk
        ]);

    }

    // Novi akter - process POST
    public function addFunkPost($app){

        $conn = Capsule::connection();
        $dump=""; 

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/funk-update.json");

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

    //proveriti da li ista funkcija vec postoji u bazi - pre dodavanja
    $resexist = $conn->table('funkcije')->where('funkcija', '=', $_POST['funkcija'])->get();
    if(count($resexist)>0){die('<div class="alert alert-danger">Funkcija koju dodajte vec postoji u bazi.</div>');}

        //insert data
        $res =  $conn->table('funkcije')->insert([  'funkcija' => $_POST['funkcija'] ]);

        if($res){
            die('<div class="alert alert-success">Funkcija dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Funkcija NIJE dodata... Kontaktirajte podrsku...</div>');
        }


    }



}



?>