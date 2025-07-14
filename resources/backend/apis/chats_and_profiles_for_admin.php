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
            "msg"=>"",
            "chats"=>[],
            "profiles"=>[]
        ];

        $messagesPerLoad=$_POST["messagesPerLoad"];
        $chatPerLoad=$_POST["chatPerLoad"];
        $user_id=$_POST["user_id"];

        $skip_chat_ids=null;

        if(isset($_POST["skip_chat_ids"])){
            $skip_chat_ids=$_POST["skip_chat_ids"];
            $skip_chat_ids=json_decode($skip_chat_ids, true);
        }

        if(!$user_id || !$chatPerLoad || !$messagesPerLoad){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $chats=ChatController::getAdminChats($user_id, $chatPerLoad, $skip_chat_ids);
        }    

        $profilesIds=[];
        if(!$res["isErr"]){
    
            $last=null;
            $index=0;
            foreach($chats as $chat){
                if(!isset($chat["skip"])){
                    $chats[$index]["messages"]=MessageController::fetchMessagesFromChatId($chat["id"], $user_id, $messagesPerLoad);
                }
    
                $newProfId=$chat["user_id"];
                if(!in_array($newProfId, $profilesIds)){
                    array_push($profilesIds, $newProfId);
                }
    
                $index++;
            }
    
            if(count($profilesIds)){
                $res["profiles"]=UserController::getUsersFromListOfIds($profilesIds);
            }
        }
    
        if(!$res["isErr"]){
            $res["chats"]=$chats;
        }
    
        echo json_encode($res);
    }