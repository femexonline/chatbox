<?php

    class StepsClass{

        static $pagesObj=array(
            "index.php"=>0,
            "review_p2.php"=>1,
            "review_p3.php"=>2,
            "proceed.php"=>3,
            "review_p4.php"=>4,
            "submitted.php"=>5,
        );
        static $pagesList=array(
            "index.php",
            "review_p2.php",
            "review_p3.php",
            "proceed.php",
            "review_p4.php",
            "submitted.php",
        );
        static $dataPagesList=array(
            "index.php",
            "review_p2.php",
            "review_p3.php",
            "review_p4.php",
        );

        static function getNextPage($name, $forSession=false){
            $key=StepsClass::$pagesObj[$name];

            $nextKey=$key+1;

            if($forSession){
                $leng=count(StepsClass::$allData);
                while($nextKey < $leng && !StepsClass::$allData[$nextKey]["data_key"]){
                    $nextKey++;
                }
            }


            if(isset(StepsClass::$pagesList[$nextKey])){
                return StepsClass::$pagesList[$nextKey];
            }

            return null;
        }

        static function getPrevPage($name, $forSession=false){
            $key=StepsClass::$pagesObj[$name];

            $prevKey=$key-1;

            if($forSession){
                while($prevKey > 0 && !StepsClass::$allData[$prevKey]["data_key"]){
                    $prevKey--;
                }
            }


            if(isset(StepsClass::$pagesList[$prevKey])){
                return StepsClass::$pagesList[$prevKey];
            }

            return null;
        }

        static $allData=array(
            array(
                "page" => "index.php",
                "data_key"=>"is_nigerian",
            ),
            array(
                "page" => "review_p2.php",
                "data_key"=>"satisfaction",
            ),
            array(
                "page" => "review_p3.php",
                "data_key"=>"econimic_performance",
            ),
            array(
                "page" => "proceed.php",
                "data_key"=>"",
            ),
            array(
                "page" => "review_p4.php",
                "data_key"=>"reelection",
            ),
        );

        static function getStepPageFromNum($key){
            return StepsClass::$allData[$key-1]["page"];
        }

        static function getStepDataFromNum($key){
            return StepsClass::$allData[$key-1]["data_key"];
        }



        static function getStepDataFromPageName($name){
            if(!$name){
                $name="index.php";
            }
            return StepsClass::$allData[StepsClass::$pagesObj[$name]]["data_key"];
        }

    }



?>