<?php
    require_once '../root_path.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';


    if($_SERVER["REQUEST_METHOD"]=="GET"){


        $res=[
            "isErr"=>false,
            "msg"=>"",
            "profile"=>null
        ];

        $dd_id_z=$_GET["dd_id_z"];


        if(!$dd_id_z){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }


        if(!$res["isErr"]){
            $res["profile"]=UserController::getBasicProfileById($dd_id_z);
        }


        echo json_encode($res);
    }
