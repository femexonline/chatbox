<?php
    require_once '../root_path.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';



        $res=[
            "isErr"=>false,
            "msg"=>"",
            "chat"=>null,
            "profile"=>null
        ];

        $messagesPerLoad=$_POST["messagesPerLoad"];
        $chatPerLoad=$_POST["chatPerLoad"];
        $chat_id=$_POST["chat_id"];


        if(!$chat_id || !$chatPerLoad || !$messagesPerLoad){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        // #################################333
        if(!$res["isErr"]){
            $chat=ChatController::getUserChats($user_id, $chatPerLoad, $skip_chat_ids);
        }    

        $profilesIds=[];
        if(!$res["isErr"]){
    
            $last=null;
            $index=0;
            foreach($chats as $chat){
                if(!isset($chat["skip"])){
                    $chats[$index]["messages"]=MessageController::fetchMessagesFromChatId($chat["id"], $user_id, $messagesPerLoad);
                }
    
                $newProfId=$chat["admin_id"];
                if(!in_array($newProfId, $profilesIds) && $newProfId){
                    array_push($profilesIds, $newProfId);
                }
    
                $index++;
            }
    
            if(count($profilesIds)){
                $res["profile"]=UserController::getUsersFromListOfIds($profilesIds);
            }
        }
    
        if(!$res["isErr"]){
            $res["chat"]=$chats;
            $res["admin_count"]=UserController::getAdminsCount();
            $res["admins"]=UserController::getAdminsMax2();
        }
    
        echo json_encode($res);
    }