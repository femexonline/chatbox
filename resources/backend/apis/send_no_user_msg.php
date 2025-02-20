<?php
    require_once '../root_path.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';

        $res=[
            "isErr"=>false,
            "msg"=>"",
            "identifier"=>null
        ];

        $msg=$_POST["msg"];

        if(!$msg){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $newUserIdentifier=UserController::addUser();

            if(!$newUserIdentifier){
                $res["isErr"]=true;
                $res["msg"]="Some error occured";

            }else{
                $user=UserController::getByIdentifier($newUserIdentifier);
                if(!$user){
                    $res["isErr"]=true;
                    $res["msg"]="Some error occured";
    
                }                    
            }
    
        }

        if(!$res["isErr"]){

            $sent=MessageController::addFirstChatMessage($newUserIdentifier, $user["id"], $msg);
            if(!$sent){
                $res["isErr"]=true;
                $res["msg"]="Some error occured";

            }                    

        }

        


        if(!$res["isErr"]){
            $res["identifier"]=$newUserIdentifier;
        }

        echo json_encode($res);
    }