<?php
    require_once '../root_path.php';
    // require_once './_access_control.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';

        $res=[
            "err"=>"",
            "isErr"=>false,
            "chatData"=>null,
            "msg"=>null,
        ];

        #chatID, senderId, msg, resId

        $msg_id=intval($_POST["msg_id"]);
        
        if(!$msg_id){
            $res["isErr"]=true;
            $res["err"]="Some error occured";
        }


        if(!$res["isErr"]){
            $res["msg"]=MessageController::getById($msg_id);

            if(!$res["msg"]){
                $res["isErr"]=true;
                $res["err"]="Some error occured";
            }
        }   
        

        if(!$res["isErr"]){
            $res["chatData"]=ChatController::getBasicDataById($res["msg"]["chat_id"]);

            if(!$res["chatData"]){
                $res["isErr"]=true;
                $res["err"]="Some error occured";
            }
        }   
            
    
        echo json_encode($res);
    }