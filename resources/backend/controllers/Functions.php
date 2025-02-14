<?php

    class Functions {
        static function testTextInput($data){
            // this function is to check text input for html special charcters, slaches
            // extra speces for removal. and also conver the input to lower case
    
            //removes back slaches
            $data=stripslashes($data);
            //remove extra space
            $data=trim($data);
            //convert to lowercase
            $data=strtolower($data);
            //converts special html characters to html entities
            $data=htmlspecialchars($data);
            return $data;
        }//function testTextInput($data) ends here


        static function testPasswordInput($data){
            // this function is to check text input for html special charcters
            //and slaches for removal
            $data=stripslashes($data);
            $data=htmlspecialchars($data);
            return $data;
        }//function testPasswordInput($data) ends here

        static function formatUrl($url="/"){
            global $ROOT_ADDR;
    
            $url=trim($url);
    
            $isRoot=true;
            if(!preg_match("/^[\/]/", $url)){
                $isRoot=false;
            }
            
            if($isRoot){
                $url=$ROOT_ADDR.$url;
            }
    
            return $url;
        }

        static function encryptPassword($pass){
            return password_hash($pass, PASSWORD_BCRYPT);
        }


        static function verifyPassword($pass, $hash){
            return password_verify($pass, $hash);
        }

        static function roundUpTo($val, $to=5){
            return ceil($val/$to)*$to;

        }

        static function getSVG($name, $static=false){
            global $ROOT_DIR;

            $svgPath="/resources/frontend/svgs/";

            if($static){
                return file_get_contents($ROOT_DIR.$svgPath."static/".$name.".svg");
            }else{
                return file_get_contents($ROOT_DIR.$svgPath.$name.".svg");
            }

        }

    }

    


    

    
?>