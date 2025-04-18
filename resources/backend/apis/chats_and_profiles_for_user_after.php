<?php
    // this is used to ged new infor fr admin after socket recnnect

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

        $user_identifier=$_POST["user_id"];
        $after=intval($_POST["after"]);


        if(!$user_identifier || !$after){
            $res["isErr"]=true;
            $res["msg"]="Some error occured";
        }

        if(!$res["isErr"]){
            $user=UserController::getByIdentifier($user_identifier, true);
            if(!$user){
                $res["isErr"]=true;
                $res["msg"]="Some error occured";
            }
        }



        if(!$res["isErr"]){
            $chats=ChatController::getUserChatsAfterTime($user["id"], $after);
        }    

        $profilesIds=[];
        if(!$res["isErr"]){
    
            $last=null;
            $index=0;
            foreach($chats as $chat){
                $chats[$index]["messages"]=MessageController::fetchMessagesFromChatIdAfterTime($chat["id"], $after);
    
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