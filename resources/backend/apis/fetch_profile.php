<?php
    require_once '../root_path.php';

    // just busy

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';

        $res=[
            "isErr"=>false,
            "msg"=>"",
            "user"=>null
        ];

        $id=$_POST["id"];
        $type=$_POST["type"];

        if(!$id){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            if($type=="admin"){
                $user=UserController::getBasicProfileById($id);
            }else{
                $user=UserController::getByIdentifier($id);
            }
            
            if(!$user){
                $res["isErr"]=true;
                $res["msg"]="User does not exist";
            }   
        }


        if(!$res["isErr"]){
            $res["user"]=$user;
        }

        echo json_encode($res);
    }