<?php
    require_once '../root_path.php';
    // require_once './_access_control.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';

        $res=[
            "err"=>"",
            "isErr"=>false,
            "data"=>[],
        ];

        #chatID, senderId, msg, resId

        $isAdmin=boolval($_POST["isAdmin"]);
        $chatsData=$_POST["chatsData"];
        $senderId=$_POST["senderId"];
        $time=$_POST["time"];

        

        if(!$chatsData || !$senderId || !$time){
            $res["isErr"]=true;
            $res["err"]="Some error occured";
        }

        if(!$res["isErr"]){
            $chatsData=json_decode($chatsData, true);

            $chatIds = array_keys($chatsData);
            $chatsDbData=ChatController::getBasicDataByIdList($chatIds, true);

            foreach($chatsData as $chat_id=>$msgIds_str){
                if(isset($chatsDbData[$chat_id])){
                    $chat=$chatsDbData[$chat_id];

                    if($chat["admin_id"]){
                        $idToRecieve=null;

                        $d=MessageController::markMessagesFromChatAsSeenFromIdStr($chat_id, $msgIds_str, $senderId, $time);
                        if($isAdmin){
                            $idToRecieve=$chat["user_id"];
                        }else{
                            $idToRecieve=$chat["admin_id"];
                        }

                        array_push($res["data"], [
                            "chat_id"=>$chat_id,
                            "idToRecieve"=>$idToRecieve,
                        ]);

                    }
                    
                }

            }    
        
        }

    
        echo json_encode($res);
    }