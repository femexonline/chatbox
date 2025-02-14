<?php
    require_once '../root_path.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';



        $res=[
            "isErr"=>false,
            "msg"=>"",
            "messages"=>[],
        ];

        $messagesPerLoad=$_POST["messagesPerLoad"];
        $chatID=$_POST["chatID"];
        $last_id=$_POST["last_id"];

        $fetchToID=null;

        if(isset($_POST["fetchToID"])){
            $fetchToID=$_POST["fetchToID"];
        }

        if(!$chatID || (!$last_id && !$fetchToID) || !$messagesPerLoad){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            if($fetchToID){
                $messages=MessageController::fetchOlderChatMsgsBefrIdTo($chatID, $last_id, $fetchToID);
            }else{
                $messages=MessageController::fetchOlderChatMessagesBeforId($chatID, $last_id, $messagesPerLoad);
            }
        }
        
        if(!$res["isErr"]){
            $res["messages"]=$messages;
        }
    
        echo json_encode($res);
    }