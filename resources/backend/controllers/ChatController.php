<?php
    require_once $ROOT_DIR.'/resources/backend/database.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';


    class ChatController{    
        
        static function _convertListToSqlList($list) {
            // Join the array elements with commas
            return "(" . implode(", ", $list) . ")";
        }
        
        static function __getChatsSQL_getUnreadChats($where){
            $chats="WITH RankedMessages AS (
                    SELECT 
                        m.id AS message_id, 
                        m.time_sent, 
                        m.chat_id,
                        m.read_status,
                        m.sender_id,
                        ROW_NUMBER() OVER (PARTITION BY m.chat_id ORDER BY m.id DESC) AS rn
                    FROM 
                        messages m
                )
                SELECT 
                    c.id, 
                    c.user_id, 
                    c.user_identifier, 
                    c.admin_id, 
                    rm.message_id, 
                    rm.time_sent,
                    rm.chat_id,
                    rm.read_status,
                    rm.sender_id
                FROM 
                    chats c
                LEFT JOIN RankedMessages rm ON c.id = rm.chat_id AND rm.rn = 1
                WHERE 
                    $where
                ORDER BY 
                    rm.time_sent DESC
                ;

            ";

            return $chats;
        }

        static function __getChatsSQL($limitToUse, $where){
            $chats="WITH RankedMessages AS (
                    SELECT 
                        m.id AS message_id, 
                        m.time_sent, 
                        m.chat_id,
                        ROW_NUMBER() OVER (PARTITION BY m.chat_id ORDER BY m.id DESC) AS rn
                    FROM 
                        messages m
                )
                SELECT 
                    c.id, 
                    c.user_id, 
                    c.user_identifier, 
                    c.admin_id, 
                    rm.message_id, 
                    rm.time_sent,
                    rm.chat_id
                FROM 
                    chats c
                LEFT JOIN RankedMessages rm ON c.id = rm.chat_id AND rm.rn = 1
                WHERE 
                    $where
                ORDER BY 
                    rm.time_sent DESC
            ";

            if($limitToUse){
                $chats.="LIMIT $limitToUse";
            }

            $chats.=";";

            return $chats;
        }


        static function _getAdminChatsSQL($limitToUse, $skip_chat_ids){
            $where="c.admin_id = :id OR c.admin_id IS NULL";
            if($skip_chat_ids){
                $where="(c.admin_id = :id OR c.admin_id IS NULL) AND c.id NOT IN $skip_chat_ids";
            }

            return ChatController::__getChatsSQL($limitToUse, $where);
        }

        static function _getUserChatsSQL($limitToUse, $skip_chat_ids){
            $where="c.user_id = :id";
            if($skip_chat_ids){
                $where="c.user_id = :id AND c.id NOT IN $skip_chat_ids";
            }

            return ChatController::__getChatsSQL($limitToUse, $where);
        }

        static function _getAdminChatsSQL_unreadMsgs($skip_chat_ids){
            $where="(c.admin_id = :id OR c.admin_id IS NULL) AND NOT rm.read_status=:read AND NOT rm.sender_id=:sender_id";
            if($skip_chat_ids){
                $where="(c.admin_id = :id OR c.admin_id IS NULL) AND NOT rm.sender_id=:sender_id AND NOT rm.read_status=:read AND c.id NOT IN $skip_chat_ids";
            }

            return ChatController::__getChatsSQL_getUnreadChats($where);
        }

        static function _getUserChatsSQL_unreadMsgs($skip_chat_ids){
            $where="c.user_id = :id AND NOT rm.read_status=:read AND NOT rm.sender_id=:sender_id";
            if($skip_chat_ids){
                $where="c.user_id = :id AND NOT rm.sender_id=:sender_id AND NOT rm.read_status=:read AND c.id NOT IN $skip_chat_ids";
            }

            return ChatController::__getChatsSQL_getUnreadChats($where);
        }
        
        static function getAdminChats($adminisId, $limit, $skip_chat_ids=null){
            global $conn;

            $limitToUse=$limit+1;

            $type="admin";

            if($skip_chat_ids){
                if(count($skip_chat_ids)){
                    $skip_chat_ids=ChatController::_convertListToSqlList($skip_chat_ids);
                }else{
                    $skip_chat_ids=null;
                }
            }else{
                $skip_chat_ids=null;
            }


            $chats=ChatController::_getAdminChatsSQL($limitToUse, $skip_chat_ids);
            $chats=$conn->prepare($chats);
            $chats->bindParam(":id", $adminisId);

            if(!$chats->execute()){
                return  null;
            }

            $chats=$chats->fetchAll(PDO::FETCH_ASSOC);

            $cont=count($chats);
            $unread=[];


            if(!$skip_chat_ids){
                $read="read";


                $skip_chat_ids=[];
                foreach($chats as $chat){
                    array_push($skip_chat_ids, $chat["id"]);
                }
                $skip_chat_ids=ChatController::_convertListToSqlList($skip_chat_ids);
    
    
                $unread=ChatController::_getAdminChatsSQL_unreadMsgs($skip_chat_ids);
                $unread=$conn->prepare($unread);
                $unread->bindParam(":id", $adminisId);
                $unread->bindParam(":sender_id", $adminisId);
                $unread->bindParam(":read", $read);
    
                if(!$unread->execute()){
                    return  null;
                }
    
                $unread=$unread->fetchAll(PDO::FETCH_ASSOC);

                if(count($unread)){
                    $unread_ids=[];
                    foreach($unread as $unre){
                        array_push($unread_ids, $unre["id"]);
                    }
                    $unread_ids=ChatController::_convertListToSqlList($unread_ids);
    
                    $latest=MessageController::getLastMessageIdForChatsIdsInList($unread_ids);
    
                    $index=0;
                    while($index < count($unread)){
                        $unread[$index]["message_id"]=$latest[$unread[$index]["chat_id"]];
                        $index++;
                    }
                }

            }
            
            if($cont > $limit && !count($unread)){
                if(!MessageController::UserHasUnreadMsgInChat($adminisId, $chats[$cont-1]["id"])){
                    $chats[$cont-1]["skip"]=true;
                }
            }
            
            if(count($unread)){
                $chats=array_merge($chats, $unread);
            }

            return $chats;
        }

        static function getAdminChatsAfterTime($adminisId, $time){
            global $conn;


            $chats=ChatController::__getChatsSQL(0, "(c.admin_id = :id OR c.admin_id IS NULL) AND rm.time_sent >= :time_start");
            $chats=$conn->prepare($chats);
            $chats->bindParam(":id", $adminisId);
            $chats->bindParam(":time_start", $time);

            if(!$chats->execute()){
                return  null;
            }

            $chats=$chats->fetchAll(PDO::FETCH_ASSOC);

            return $chats;
        }

        static function getUserChats($userId, $limit, $skip_chat_ids=null){
            global $conn;

            $limitToUse=$limit+1;


            if($skip_chat_ids){
                if(count($skip_chat_ids)){
                    $skip_chat_ids=ChatController::_convertListToSqlList($skip_chat_ids);
                }else{
                    $skip_chat_ids=null;
                }
            }else{
                $skip_chat_ids=null;
            }


            $chats=ChatController::_getUserChatsSQL($limitToUse, $skip_chat_ids);
            $chats=$conn->prepare($chats);
            $chats->bindParam(":id", $userId);

            if(!$chats->execute()){
                return  null;
            }

            $chats=$chats->fetchAll(PDO::FETCH_ASSOC);

            $cont=count($chats);
            $unread=[];


            if(!$skip_chat_ids){
                $read="read";


                $skip_chat_ids=[];
                foreach($chats as $chat){
                    array_push($skip_chat_ids, $chat["id"]);
                }
                $skip_chat_ids=ChatController::_convertListToSqlList($skip_chat_ids);
    
    
                $unread=ChatController::_getUserChatsSQL_unreadMsgs($skip_chat_ids);
                $unread=$conn->prepare($unread);
                $unread->bindParam(":id", $userId);
                $unread->bindParam(":sender_id", $userId);
                $unread->bindParam(":read", $read);
    
                if(!$unread->execute()){
                    return  null;
                }
    
                $unread=$unread->fetchAll(PDO::FETCH_ASSOC);

                if(count($unread)){
                    $unread_ids=[];
                    foreach($unread as $unre){
                        array_push($unread_ids, $unre["id"]);
                    }
                    $unread_ids=ChatController::_convertListToSqlList($unread_ids);
    
                    $latest=MessageController::getLastMessageIdForChatsIdsInList($unread_ids);
    
                    $index=0;
                    while($index < count($unread)){
                        $unread[$index]["message_id"]=$latest[$unread[$index]["chat_id"]];
                        $index++;
                    }
                }

            }
            
            if($cont > $limit && !count($unread)){
                if(!MessageController::UserHasUnreadMsgInChat($userId, $chats[$cont-1]["id"])){
                    $chats[$cont-1]["skip"]=true;
                }
            }
            
            if(count($unread)){
                $chats=array_merge($chats, $unread);
            }

            return $chats;
        }

        static function getUserChatsAfterTime($userId, $time){
            global $conn;


            $chats=ChatController::__getChatsSQL(0, "c.user_id = :id AND rm.time_sent >= :time_start");
            $chats=$conn->prepare($chats);
            $chats->bindParam(":id", $userId);
            $chats->bindParam(":time_start", $time);

            if(!$chats->execute()){
                return  null;
            }

            $chats=$chats->fetchAll(PDO::FETCH_ASSOC);

            return $chats;
        }

        
        static function addChat($user_id, $user_identifier){
            global $conn;

            $sql="INSERT INTO chats (user_id, user_identifier) VALUES 
                (:user_id, :user_identifier)
            ";

            $sql=$conn->prepare($sql);
            $sql->bindParam(":user_id", $user_id);
            $sql->bindParam(":user_identifier", $user_identifier);

            if($sql->execute()){
                return $conn->lastInsertId();
            }else{
                return false;
            }


        }

        static function getById($id){
            global $conn;


            $where="c.id=:id";

            $chat=ChatController::__getChatsSQL(1, $where);
            $chat=$conn->prepare($chat);
            $chat->bindParam(":id", $id);

            if(!$chat->execute()){
                return  null;
            }

            $chat=$chat->fetch(PDO::FETCH_ASSOC);

            
            return $chat;
        }


    }


?>