<?php
    require_once '../root_path.php';
    // require_once './_access_control.php';

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
        require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';

        $res=[
            "err"=>"",
            "isErr"=>false,
            "adminFirstRes"=>false,
            "pingNotTheAdmin"=>false,
            "chatData"=>null,
            "msg"=>null,
        ];

        #chatID, senderId, msg, resId

        $isAdmin=boolval($_POST["isAdmin"]);
        $chatID=intval($_POST["chatID"]);
        $senderId=intval($_POST["senderId"]);
        $msg=$_POST["msg"];
        $resId=null;

        $sendMsg=true;

        if(isset($_POST["resId"])){
            $resId=intval($_POST["resId"]);
        }

        

        if(!$chatID || !$senderId || !$msg){
            $res["isErr"]=true;
            $res["err"]="Some error occured";
        }
        

        if(!$res["isErr"]){
            $res["chatData"]=ChatController::getBasicDataById($chatID);

            if(!$res["chatData"]){
                $res["isErr"]=true;
                $res["err"]="Some error occured";
            }
        }   
        
        if(!$res["isErr"] && $isAdmin){
            if(!$res["chatData"]["admin_id"]){
                if(ChatController::setChatAdminId($chatID, $senderId)){
                    $res["adminFirstRes"]=true;
                }else{
                    $res["isErr"]=true;
                    $res["err"]="Some error occured";
                }
            }else{
                if(intval($res["chatData"]["admin_id"])!=intval($senderId)){
                    $res["pingNotTheAdmin"]=true;
                    $sendMsg=false;
                }
            }

        }

        if(!$res["isErr"] && $sendMsg){
            $newMsgId=MessageController::addMessage($chatID, $senderId, $msg);
            if(!$newMsgId){
                $res["isErr"]=true;
                $res["err"]="Some error occured";
            }else{
                $res["msg"]=MessageController::getById($newMsgId);
            }
        }
    
    
        echo json_encode($res);
    }