<?php
    require_once '../root_path.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';

        $res=[
            "isErr"=>false,
            "msg"=>"",
            "temp_identifier"=>null,
            "chat_id"=>null,
            "chat"=>null,
            "messages"=>null,
        ];

        $msg=$_POST["msg"];
        $user_id=$_POST["user_id"];
        $temp_identifier=$_POST["temp_identifier"];
        $messagesPerLoad=$_POST["messagesPerLoad"];

        $res["temp_identifier"]=$temp_identifier;

        if(!$msg || !$user_id || !$temp_identifier){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $user=UserController::getBasicProfileById($user_id);
            if(!$user){
                $res["isErr"]=true;
                $res["msg"]="Some error occured";
            }                    
    
        }

        if(!$res["isErr"]){

            $sent=MessageController::addFirstChatMessage($user["identifier"], $user["id"], $msg, true);
            if(!$sent){
                $res["isErr"]=true;
                $res["msg"]="Some error occured";
            }else{
                $res["chat_id"]=$sent;
                $res["chat"]=ChatController::getById($res["chat_id"]);
                $res["messages"]=MessageController::fetchMessagesFromChatId($res["chat_id"], $user_id, $messagesPerLoad);
            } 
            
        }

        
        echo json_encode($res);
    }