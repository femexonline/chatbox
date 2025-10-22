<?php
    require_once $ROOT_DIR.'/resources/backend/database.php';


    class UserController{

        static function _convertListToSqlList($list) {
            if(!count($list)){
                return null;
            }
            
            // Join the array elements with commas
            return "(" . implode(", ", $list) . ")";
        }


        static function addAdmin($f_name, $l_name, $email, $password){
            global $conn;

            $type="admin";
            $p_pix="default.jpg";

            $password_hashed=password_hash($password, PASSWORD_BCRYPT);

            $add="INSERT INTO users (
                    f_name, l_name, email, type, p_pix, password
                ) VALUES (
                    :f_name, :l_name, :email, :type, :p_pix, :password
                )"
            ;
            $add=$conn->prepare($add);
            $add->bindParam(":f_name", $f_name);
            $add->bindParam(":l_name", $l_name);
            $add->bindParam(":email", $email);
            $add->bindParam(":type", $type);
            $add->bindParam(":p_pix", $p_pix);
            $add->bindParam(":password", $password_hashed);
            
            if($add->execute()){
                return $conn->lastInsertId();
            }else{
                return false;
            }

        }

        private static function _addUser(){
            global $conn;

            $type="user";
            $p_pix="default.jpg";


            $add="INSERT INTO users (type, p_pix) VALUES (:type, :p_pix)";
            $add=$conn->prepare($add);
            $add->bindParam(":type", $type);
            $add->bindParam(":p_pix", $p_pix);
            
            if($add->execute()){
                return $conn->lastInsertId();
            }else{
                return false;
            }

        }
        static function addUser(){
            global $conn;

            $user_id=UserController::_addUser();

            if(!$user_id){
                return null;
            }

            $time=time();
            $identifier=$user_id."_".$time.$user_id;

            $sql="UPDATE users SET identifier=:identifier WHERE id=:id";
            $sql=$conn->prepare($sql);
            $sql->bindParam(":identifier", $identifier);
            $sql->bindParam(":id", $user_id);


            if(!$sql->execute()){
                return null;
            }  
            
            return $identifier;
        }


        static function getAdminByEmail($email){
            global $conn;

            $type="admin";

            $review="SELECT * FROM users WHERE email=:email AND type=:type";

            $review=$conn->prepare($review);
            $review->bindParam(":email", $email);
            $review->bindParam(":type", $type);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetch(PDO::FETCH_ASSOC);

        }


        static function emailExist($email){
            global $conn;
            
            $userEmail="SELECT id FROM users WHERE email=:email";
            $userEmail=$conn->prepare($userEmail);
            $userEmail->bindParam(':email', $email);
            $userEmail->execute();
            $userEmail=$userEmail-> fetch(PDO::FETCH_ASSOC);


            return $userEmail !== false;
        }
        

        static function getBasicProfileById($id){
            global $conn;

            $review="SELECT id, f_name, l_name, type, identifier, p_pix FROM users WHERE id=:id";

            $review=$conn->prepare($review);
            $review->bindParam(":id", $id);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetch(PDO::FETCH_ASSOC);

        }

        static function getProfLastseenFromList($list){
            global $conn;

            $list=UserController::_convertListToSqlList($list);

            $review="SELECT id, last_seen FROM users WHERE id IN $list";

            $review=$conn->prepare($review);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetchAll(PDO::FETCH_ASSOC);

        }
        
        static function getById($id){
            global $conn;

            $review="SELECT * FROM users WHERE id=:id";

            $review=$conn->prepare($review);
            $review->bindParam(":id", $id);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetch(PDO::FETCH_ASSOC);

        }

        static function getByIdentifier($identifier, $forId=false){
            global $conn;

            $review="SELECT id, f_name, l_name, type, identifier, p_pix, last_seen FROM users WHERE identifier=:identifier";

            if($forId){
                $review="SELECT id FROM users WHERE identifier=:identifier";
            }

            $review=$conn->prepare($review);
            $review->bindParam(":identifier", $identifier);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetch(PDO::FETCH_ASSOC);

        }

        static function getUsersFromListOfIds($idList){
            global $conn;

            $sqlList=UserController::_convertListToSqlList($idList);

            $sql = "SELECT id, f_name, l_name, type, identifier, p_pix, last_seen FROM users WHERE id IN $sqlList;";

            $sql=$conn->prepare($sql);

            if(!$sql->execute()){
                return  null;
            }

            return $sql->fetchAll(PDO::FETCH_ASSOC);
            
        }

        static function getAdminsMax2(){
            global $conn;

            $type="admin";

            $review="SELECT id, f_name, l_name, type, identifier, p_pix 
                FROM users WHERE type=:type LIMIT 3
            ";

            $review=$conn->prepare($review);
            $review->bindParam(":type", $type);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetchAll(PDO::FETCH_ASSOC);
        }

        static function getAdminsCount(){
            global $conn;

            $type="admin";

            $review="SELECT COUNT(id) AS count FROM users WHERE type=:type";

            $review=$conn->prepare($review);
            $review->bindParam(":type", $type);

            if(!$review->execute()){
                return  null;
            }

            return $review->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
        }
        
        //for socket2 api
        static function markUserAsOnline($id){
            global $conn;

            $sql = "UPDATE users SET last_seen = 'online' WHERE id =:id";
            $sql=$conn->prepare($sql);
            $sql->bindParam(":id", $id);
            
            if($sql->execute()){
                return "online";
            }

            return false;
        }

        //for socket2 api
        static function markUserAsOffline($id, $time){
            global $conn;

            $sql = "UPDATE users SET last_seen =:timee WHERE id =:id";
            $sql=$conn->prepare($sql);
            $sql->bindParam(":id", $id);
            $sql->bindParam(":timee", $time);
            
            if($sql->execute()){
                return $time;
            }

            return false;
        }

    }


?>