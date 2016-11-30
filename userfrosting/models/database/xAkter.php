<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xAkter Class
 *
 * CRU za aktere
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

class xAkter extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "akteri";


    public function listaAktera($app){

        $conn = Capsule::connection();
        $res = $conn->table('akteri')->orderBy('aid','desc')->get();

        $app->render('akteri.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "akteri" => $res
        ]);


    }

    // list akter fields
    public function editAktera($app,$aid){

        $conn = Capsule::connection();
        $res = $conn->table('akteri')->where('aid', '=', $aid)->get();

        $dump= "user edit for no: ".$aid;
        //$dump= print_r($res,true);

        $app->render('akterEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "akteri" => $res
        ]);

    }

    // update aktera - process POST
    public function editAkteraPost($app,$aid){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/akter-update.json");
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
$restest = $conn->table('akteri')->where('aid', '=', $aid)->get();
if( $restest[0]['aime'] == $_POST['ime'] && $restest[0]['aprezime'] == $_POST['prezime'] && $restest[0]['azanimanje'] == $_POST['zanimanje'] && $restest[0]['arodjen'] == $_POST['rodjen'] && $restest[0]['apol'] == $_POST['pol'] && $restest[0]['abio'] == $_POST['bio']    )
{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }


        //UPDATE TABLE DATA
        $res =  $conn->table('akteri')->where('aid', '=', $aid)->update([  'aime' => $_POST['ime'] , 'aprezime' => $_POST['prezime'] , 'azanimanje' => $_POST['zanimanje'] , 'arodjen' => $_POST['rodjen'] , 'apol' => $_POST['pol'] , 'abio' => $_POST['bio']  ]);

        if($res){
            die('<div class="alert alert-success">User updated.</div>');
            }else {
            die('<div class="alert alert-danger">User NOT updated... Something is wrong OR NO CHANGES SENT</div>');
            }

        //kod za standardni post
        $dump .= "\r\n".print_r($_POST,true);
        $dump .= "\r\n".print_r($ms,true);

        $resAftUpd = $conn->table('akteri')->where('aid', '=', $aid)->get();

        $app->render('akterEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "akteri" => $resAftUpd
        ]);

    }

    // Novi akter - forma
    public function addAktera($app){

        $dump= "Add new akter: ";
        //$dump= print_r($res,true);
        $dummyAkter = array(array());

        $app->render('akterEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "akteri" => $dummyAkter
        ]);

    }

    // Novi akter - process POST
    public function addAkteraPost($app){

        $conn = Capsule::connection();

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/akter-update.json");


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


//TODO provera da li akter koji se ubaciju vec postoji u bazi

        //insert data
        $res =  $conn->table('akteri')->insert([  'aime' => $_POST['ime'] , 'aprezime' => $_POST['prezime'] , 'azanimanje' => $_POST['zanimanje'] , 'arodjen' => $_POST['rodjen'] , 'apol' => $_POST['pol'] , 'abio' => $_POST['bio']  ]);

        if($res){
            die('<div class="alert alert-success">New akter added.</div>');
        }else {
            die('<div class="alert alert-danger">Unable to add new akter... Contact support.</div>');
        }


    }



}



?>