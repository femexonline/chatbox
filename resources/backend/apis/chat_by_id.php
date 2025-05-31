<?php
    require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';
    // require_once '../root_path.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';



        $res=[
            "isErr"=>false,
            "chat"=>null,
            "profile"=>null
        ];

        $messagesPerLoad=$_POST["messagesPerLoad"];
        $chatPerLoad=$_POST["chatPerLoad"];
        $chat_id=$_POST["chat_id"];
        $user_id=intval($_POST["user_id"]);


        if(!$chat_id || !$user_id || !$chatPerLoad || !$messagesPerLoad){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $chat=ChatController::getById($chat_id);
        }    

        $profilesId=null;
        if(!$res["isErr"]){
            if($chat){
                $profilesId=$chat["user_id"];
                if($profilesId==$user_id){
                    $profilesId=$chat["admin_id"];
                }


                if($profilesId){
                    $res["profile"]=UserController::getBasicProfileById($profilesId);
                }
            }

        }
    
        if(!$res["isErr"]){
            $res["chat"]=$chat;
            $res["chat"]["messages"]=MessageController::fetchMessagesFromChatId($chat["id"], $user_id, $messagesPerLoad);
        }
    
        echo json_encode($res);
    }