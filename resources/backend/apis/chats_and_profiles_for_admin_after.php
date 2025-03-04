<?php
    require_once '../root_path.php';

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

        $user_id=$_POST["user_id"];
        $after=intval($_POST["after"]);


        if(!$user_id || !$after){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }


        if(!$res["isErr"]){
            $chats=ChatController::getAdminChatsAfterTime($user_id, $after);
        }    

        $profilesIds=[];
        if(!$res["isErr"]){
    
            $last=null;
            $index=0;
            foreach($chats as $chat){
                // get all messages after time
                $chats[$index]["messages"]=MessageController::fetchMessagesFromChatIdAfterTime($chat["id"], $user_id, $after);
    
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