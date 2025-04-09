<?php
    require_once '../root_path.php';

    // to fetch kast seen
    // to fetch kast seen
    // to fetch kast seen
    // to fetch kast seen
    // to fetch kast seen
    // to fetch kast seen

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';

        $res=[
            "isErr"=>false,
            "msg"=>"",
            "data"=>[]
        ];

        $list=$_POST["list"];

        if(!$list){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $list=json_decode($list);
            $data=UserController::getProfLastseenFromList($list);
        }


        if(!$res["isErr"]){
            $res["data"]=$data;
        }

        echo json_encode($res);
    }