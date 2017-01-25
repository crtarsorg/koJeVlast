<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * xFunk Class
 *
 * CRU za opstine
 *
 */


class xOpstine extends UFModel {
    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "opstine";


    public function listaOpstina($app){

        $conn = Capsule::connection();
        $res = $conn->table('opstine')->orderBy('opstina','asc')->get();
        $dump= print_r($res,true);

        $app->render('opstine.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            //"akteri" => isset($akteri) ? $akteri : []
            "opstine" => $res
        ]);


    }

    // list akter fields
    public function editOpstina($app,$oid){

        $conn = Capsule::connection();
        $res = $conn->table('opstine')->where('opid', '=', $oid)->get();

        $dump= "edit for no: ";
        //$dump= print_r($res,true);

        $app->render('opstineEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Update",
            "opstine" => $res
        ]);

    }

    // update aktera - process POST
    public function editOpstinaPost($app,$oid){

        $conn = Capsule::connection();
        $dump="";

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/opstine-update.json");
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
//$restest = $conn->table('opstine')->where('opid', '=', $oid)->get();
//if( $restest[0]['opstina'] == $_POST['opstina']    )
//{ die('<div class="alert alert-danger">Data you submited is the <b>SAME</b> as data in database. Aborting...</div>') ; }

////proveriti da li ista opstina vec postoji u bazi - pre dodavanja
//$resexist = $conn->table('opstine')->where('opstina', '=', $_POST['opstina'])->get();
//if(count($resexist)>0){die('<div class="alert alert-danger">Opstina koju menjate vec postoji u bazi (nova vrednost koju ste poslali vec postoji u bazi).</div>');}

//$fp = fopen( __DIR__ .'../../../../../public_html/files/docs/pubht.txt', 'w');
//fwrite($fp, '1');
//fwrite($fp, '23');
//fclose($fp);



        //UPDATE TABLE DATA
        $res =  $conn->table('opstine')->where('opid', '=', $oid)->update([  'opstina' => $_POST['opstina'],'opov' => $_POST['povrsina'],'opop' => $_POST['stanovnika'],'olink' => $_POST['link'] ]);

        if($res){
            die('<div class="alert alert-success">Opstina updated.</div>');
            }else {
            die('<div class="alert alert-danger">Opstina NOT updated... Something is wrong OR NO CHANGES SENT</div>');
            }
    }


    // Novi akter - forma
    public function addOpstina($app){

        $dump= "Dodaj novu opstinu: ";
        //$dump= print_r($res,true);
        $dummyOpstine = array(array());

        $app->render('opstineEdit.twig', [
            "paginate_server_side" => false,
            "dump" => $dump,
            "actionText" => "Add New",
            "opstine" => $dummyOpstine
        ]);

    }

    // Novi akter - process POST
    public function addOpstinaPost($app){

        $conn = Capsule::connection();
        $dump="";

        ///Validacija POST-a
        $post = $app->request->post();
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/opstine-update.json");

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
$resexist = $conn->table('opstine')->where('opstina', '=', $_POST['opstina'])->get();
if(count($resexist)>0){die('<div class="alert alert-danger">Opstina koju dodajte vec postoji u bazi.</div>');}

        //insert data
        $res =  $conn->table('opstine')->insert([  'opstina' => $_POST['opstina'] ]);

        if($res){
            die('<div class="alert alert-success">Opstina dodata.</div>');
        }else {
            die('<div class="alert alert-danger">Opstina NIJE dodata... Kontaktirajte podrsku...</div>');
        }


    }


    public function opstinaAddDocPost($app){

        $conn = Capsule::connection();
        $dump="";
        $resp="";

        ///Validacija POST-a
        $post = $app->request->post();

        //var_dump($post);

        if(file_exists(  __DIR__ .'../../../../../public_html/files/docs/' . $_FILES['file']['name']  )){
            die('<div class="alert alert-danger">Fajl sa tim imenom vec postoji.</div>');
        }

        if(!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            echo '<div class="alert alert-danger">Niste poslali fajl.</div>';
        }
        else {
            $r = move_uploaded_file($_FILES['file']['tmp_name'],  __DIR__ .'../../../../../public_html/files/docs/' . $_FILES['file']['name']);
            if($r){
                $resp = "File copied to server. ";
                //ubaci fajl u bazu
                $res =  $conn->table('opdocs')->insert([  'opdown' => $_POST['owner'],'opdnaziv' => $_POST['naziv'],'opdfile' => $_FILES['file']['name'],'opdkat' => $_POST['cat'] ]);
                if($res){$resp .= "<br>File inserted to database. ";} else {$resp .= "<br>Error.. File NOT inserted to database. ";}


                //finish if ok
                echo '<div class="alert alert-info">'.$resp.'</div>';

            } else {$resp = "Error... File NOT copied to server. ";}
        }

        //var_dump($post);
        //var_dump($_FILES);
        die();



    }



    // API - return docs uploaded for opstina
    public function opstinaGetDocs($app,$docid){

        $conn = Capsule::connection();
        $dump="";

        $res = $conn->table('opdocs')->where('opdown', '=', $docid)->orderby('opdkat')->get();

        for($i = 0; $i < count($res); $i++) {

            switch ($res[$i]['opdkat']) {
                case "1":
                    $res[$i]['opdkatlabel']="Budžet";
                    break;
                case "2":
                    $res[$i]['opdkatlabel']="Rezultati izbora";
                    break;
                case "3":
                    $res[$i]['opdkatlabel']="Prinudna uprava";
                    break;
                case "4":
                    $res[$i]['opdkatlabel']="Transfer iz budžeta";
                    break;
                case "5":
                    $res[$i]['opdkatlabel']="Izvršenje budžeta";
                    break;
                default:
                    $res[$i]['opdkatlabel']="Nekategorizovano";
            }

        }

        echo json_encode($res);

    }



}



?>