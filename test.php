<?php
    require_once './resources/backend/root_path.php';

    require_once $ROOT_DIR.'/resources/backend/session-start.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';
    



    $res=[
        "isErr"=>false,
        "msg"=>"",
        "chats"=>[],
        "profiles"=>[]
    ];

    $user_id=1;
    $after=1740520407;


    if(!$res["isErr"]){
        $chats=ChatController::getAdminChatsAfterTime($user_id, $after);
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
