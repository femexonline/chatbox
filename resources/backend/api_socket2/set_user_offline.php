<?php
    require_once '../root_path.php';
    // require_once './_access_control.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';


        $res=[
            "err"=>"",
            "isErr"=>false,
            "user_status"=>"",
            "list_to_notify"=>null,
        ];

        
        $isAdmin=boolval($_POST["isAdmin"]);
        $user_id=intval($_POST["user_id"]);
        $time=$_POST["time"];
        
        if(!$user_id || !$time){
            $res["isErr"]=true;
            $res["err"]="Some error occured";
        }


        if(!$res["isErr"]){
            $res["user_status"]=UserController::markUserAsOffline($user_id, $time);
            if(!$res["user_status"]){
                $res["isErr"]=true;
                $res["err"]="Some error occured";
            }
        }   
        

        if(!$res["isErr"]){
            $list_to_notify=null;

            if(!$isAdmin){
                $res["list_to_notify"]=ChatController::getAdminIdsConnectedUserAndOnline($user_id);
            }else{
                $res["list_to_notify"]=ChatController::getUserIdsConnectedAdminAndOnline($user_id);
            }
        }   
            
    
        echo json_encode($res);
    }