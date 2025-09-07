<?php
    require_once $ROOT_DIR.'/resources/backend/database.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';


    class MessageController{
        static function _convertListToSqlList($list) {
            if(!count($list)){
                return null;
            }
            // Join the array elements with commas
            return "(" . implode(", ", $list) . ")";
        }
        
        static function addMessage($chat_id, $sender_id, $msg){
            global $conn;

            $read_status="sent";
            $time_sent=time();

            $sql="INSERT INTO messages (chat_id, sender_id, msg, time_sent, read_status) VALUES 
                (:chat_id, :sender_id, :msg, :time_sent, :read_status)
            ";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chat_id);
            $sql->bindParam(":sender_id", $sender_id);
            $sql->bindParam(":msg", $msg);
            $sql->bindParam(":time_sent", $time_sent);
            $sql->bindParam(":read_status", $read_status);

            if($sql->execute()){
                return $conn->lastInsertId();
            }else{
                return false;
            }

        }

        static function oldestUnreadChatMsgId($chat_id, $userId){
            global $conn;

            $read_status="read";

            $sql="SELECT id FROM messages WHERE chat_id=:chat_id 
                AND NOT sender_id=:sender_id AND NOT read_status=:read_status
                ORDER BY id ASC LIMIT 1
            ";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chat_id);
            $sql->bindParam(":sender_id", $userId);
            $sql->bindParam(":read_status", $read_status);

            if(!$sql->execute()){
                return  null;
            }

            $data=$sql->fetch(PDO::FETCH_ASSOC);

            if($data){
                $data=intval($data["id"]);
            }else{
                $data=null;
            }

            return $data;
        }

        static function addFirstChatMessage($user_identifier, $sender_id, $msg){

            $chat_id=ChatController::addChat($sender_id, $user_identifier);

            if(!$chat_id){
                return null;
            }

            return MessageController::addMessage($chat_id, $sender_id, $msg);
        }

        static function fetchMessagesFromChatId($chat_id, $userId, $messagesPerLoad){
            global $conn;

            $msgToLoad=$messagesPerLoad+1;

            $sql="SELECT * FROM messages WHERE chat_id=:chat_id ORDER BY id DESC LIMIT $msgToLoad";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chat_id);

            if(!$sql->execute()){
                return  null;
            }


            $data=$sql->fetchAll(PDO::FETCH_ASSOC);

            $do_skip=true;

            $cont=count($data);
            $minId=intval($data[$cont-1]["id"]);

            $firstUnreadMsgId=MessageController::oldestUnreadChatMsgId($chat_id, $userId);

            if($firstUnreadMsgId){
                if($firstUnreadMsgId < $minId){
                    $do_skip=false;

                    $aditionData=MessageController::_fetchMsgFromChatIdByMsgIdRange($chat_id, $firstUnreadMsgId, $minId-1);

                    $data=array_merge($data, $aditionData);

                }else{
                    if($firstUnreadMsgId == $minId){
                        $do_skip=false;
                    }
                }
            }



            if($cont > $messagesPerLoad && $do_skip){
                $data[$cont-1]["skip"]=true;
            }

            return $data;

        }

        static function fetchMessagesFromChatIdAfterTime($chat_id, $time_start){
            global $conn;

            $sql="SELECT * FROM messages WHERE chat_id=:chat_id AND time_sent >= :time_start ORDER BY id DESC";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chat_id);
            $sql->bindParam(":time_start", $time_start);

            if(!$sql->execute()){
                return  null;
            }


            $data=$sql->fetchAll(PDO::FETCH_ASSOC);

            return $data;

        }

        static function getReadStatOfMsgFromList($list){
            global $conn;

            $read_status="sent";
            $list=MessageController::_convertListToSqlList($list);

            $review="SELECT id, chat_id, read_status, deliver_time, read_time FROM messages WHERE id IN $list AND read_status !=:read_status";

            $review=$conn->prepare($review);
            $review->bindParam(":read_status", $read_status);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetchAll(PDO::FETCH_ASSOC);
        }

        static function _fetchMsgFromChatIdByMsgIdRange($chat_id, $frm, $to){
            global $conn;

            $sql="SELECT * FROM messages WHERE chat_id=:chat_id AND id>=:frm AND id<=:too ORDER BY id DESC";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chat_id);
            $sql->bindParam(":frm", $frm);
            $sql->bindParam(":too", $to);

            if(!$sql->execute()){
                return  null;
            }

            $data=$sql->fetchAll(PDO::FETCH_ASSOC);

            return $data;

        }

        static function fetchOlderChatMessagesBeforId($chatID, $befreId, $messagesPerLoad){
            global $conn;

            $sql="SELECT * FROM messages WHERE chat_id=:chat_id AND id<:too ORDER BY id DESC LIMIT $messagesPerLoad";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chatID);
            $sql->bindParam(":too", $befreId);

            if(!$sql->execute()){
                return  null;
            }

            $data=$sql->fetchAll(PDO::FETCH_ASSOC);

            return $data;

        }

        static function fetchOlderChatMsgsBefrIdTo($chatID, $befreId, $to){

        }

        static function UserHasUnreadMsgInChat($userId, $chatId){
            global $conn;

            $read="read";

            $sql="SELECT id FROM messages WHERE chat_id=:chat_id AND NOT read_status=:read AND NOT sender_id=:sender_id";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":chat_id", $chatId);
            $sql->bindParam(":sender_id", $userId);
            $sql->bindParam(":read", $read);

            if(!$sql->execute()){
                return  null;
            }


            $data=$sql->fetch(PDO::FETCH_ASSOC);

            if($data){
                return true;
            }else{
                return false;
            }
        }

        static function getLastMessageIdForChatsIdsInList($chatId_list){
            global $conn;

            $sql="SELECT chat_id, max(id) AS message_id FROM messages WHERE chat_id IN $chatId_list GROUP BY chat_id";

            $sql=$conn->prepare($sql);

            if(!$sql->execute()){
                return  null;
            }

            
            $res=[];
            $data=$sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $dat){
                $res[$dat["chat_id"]]=$dat["message_id"];
            }
            
            return $res;
        }

        static function getById($id){
            global $conn;

            $sql="SELECT * FROM messages WHERE id=:id";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":id", $id);

            if(!$sql->execute()){
                return  null;
            }


            $data=$sql->fetch(PDO::FETCH_ASSOC);

            return $data;
        }
    }


