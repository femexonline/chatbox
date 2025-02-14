<?php

    class Answers{
        static $type1Ans=array(
            "1"=>"Very dissatisfied",
            "2"=>"Dissatisfied",
            "3"=>"Neutral",
            "4"=>"Satisfied",
            "5"=>"Very satisfied",
        );

        static $type2Ans=array(
            "1"=>"Very poor",
            "2"=>"Poor",
            "3"=>"Fiar",
            "4"=>"Good",
            "5"=>"Very good",
        );

        static function getType1($ans){
            if(isset(Answers::$type1Ans[$ans])){
                return Answers::$type1Ans[$ans];
            }else{
                return $ans;
            }
        }

        static function getType2($ans){
            if(isset(Answers::$type2Ans[$ans])){
                return Answers::$type2Ans[$ans];
            }else{
                return $ans;
            }
        }

        static function getType3($ans){
            
            return ucfirst($ans);
        }
    }


?>