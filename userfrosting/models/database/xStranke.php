<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xStranke Class
 *
 * CRU za stranke
 *
 */


class xStranke extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "stranke";


    public function listaStranki($app){

        $conn = Capsule::connection();
        $res = $conn->table('stranke')->orderBy('snaziv','asc')->get();
        $dump= print_r($res,true);

        $app->render('stranke.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "stranke" => $res
        ]);


    }

    // list akter fields
    public function editStranka($app,$sid){

        $conn = Capsule::connection();
        $res = $conn->table('stranke')->where('sid', '=', $sid)->get();

        $dump= "edit for no: ".$sid;
        //$dump= print_r($res,true);

        $app->render('strankeEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "stranke" => $res
        ]);

    }

    // update aktera - process POST
    public function editStrankaPost($app,$sid){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/stranke-update.json");
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
$restest = $conn->table('stranke')->where('sid', '=', $sid)->get();
if( $restest[0]['snaziv'] == $_POST['stranka']    )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }

//proveriti da li ista opstina vec postoji u bazi - pre dodavanja
$resexist = $conn->table('stranke')->where('snaziv', '=', $_POST['stranka'])->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Stranka koju menjate vec postoji u bazi (nova vrednost koju ste poslali vec postoji u bazi).</div>');}


        //UPDATE TABLE DATA
        $res =  $conn->table('stranke')->where('sid', '=', $sid)->update([  'snaziv' => $_POST['stranka'] ]);

        if($res){
            die('<div class="alert alert-success">Stranka updated.</div>');
            }else {
            die('<div class="alert alert-danger">Stranka NOT updated... Something is wrong OR NO CHANGES SENT</div>');
            }
    }


    // Novi akter - forma
    public function addStranka($app){

        $dump= "Dodaj novu stranku: ";
        //$dump= print_r($res,true);
        $dummyStranke = array(array());

        $app->render('strankeEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "stranke" => $dummyStranke
        ]);

    }

    // Novi akter - process POST
    public function addStrankaPost($app){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/stranke-update.json");

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
$resexist = $conn->table('stranke')->where('snaziv', '=', $_POST['stranka'])->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Stranka koju dodajte vec postoji u bazi.</div>');}

        //insert data
        $res =  $conn->table('stranke')->insert([  'snaziv' => $_POST['stranka'] ]);

        if($res){
            die('<div class="alert alert-success">Stranka dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Stranka NIJE dodata... Kontaktirajte podrsku...</div>');
        }


    }



}



?>