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
        $res = $conn->table('promene')->select("popstina","snaziv","pnavlasti","opstina")->leftJoin('akteri', 'posoba', '=', 'aid')->leftJoin('stranke', 'pstranka', '=', 'sid')->leftJoin('funkcije', 'pfunkcija', '=', 'fid')->leftJoin('koalicije', 'pkoalicija', '=', 'kid')->leftJoin('funkcije_mesto', 'pfm', '=', 'fmid')->leftJoin('opstine', 'popstina', '=', 'opid')->groupby("popstina","pnavlasti","pstranka")->get();

        //prepare result
        $out = array();
        foreach ($res as $val) {
            //echo print_r($val,true)."<br>";

            if($val['popstina']){
                $out[$val['opstina']]['opstina'] = $val['opstina'] ;

                if($val['pnavlasti']==1){$out[$val['opstina']]['vlast'][]= $val['snaziv']; }
                if($val['pnavlasti']==2){$out[$val['opstina']]['opozicija'][]= $val['snaziv']; }
            }
        }
        echo json_encode($out);

    }




}



?>