<?php
    require_once $ROOT_DIR."/resources/backend/classes/Steps.php";



    class SessionManager{


        static function setStepData($ans, $pageName){
            $_SESSION[StepsClass::getStepDataFromPageName($pageName)]=$ans;
        }

        static function getStepData($pageName){
            if(isset($_SESSION[StepsClass::getStepDataFromPageName($pageName)])){
                return $_SESSION[StepsClass::getStepDataFromPageName($pageName)];
            }
            return null;
        }


        static function step_check($pageName){
            global $ROOT_ADDR;

            $prevPage=StepsClass::getPrevPage($pageName, true);


            if(!SessionManager::getStepData($prevPage)){
                
                $goto="";
                $useFirstPage=true;
                $index=StepsClass::$pagesObj[$prevPage];
                while($index > 0){
                    $index--;

                    $goto=StepsClass::$pagesList[$index];
                    if(SessionManager::getStepData($goto)){
                        $useFirstPage=false;
                        break;
                    }
                }

                if($useFirstPage){
                    header("location:".$ROOT_ADDR."/".StepsClass::$pagesList[0]);
                }else{
                    header("location:".$ROOT_ADDR."/".StepsClass::getNextPage($goto));
                }
                die();


            }
        }


        static function get_unsettled_page(){
            $unsettledPage="";

            $goto="";
            $index=0;
            $cunt=count(StepsClass::$allData);
            while($index < $cunt){
                if(StepsClass::$allData[$index]["data_key"]){
                    $goto=StepsClass::$allData[$index]["page"];
                    if(!SessionManager::getStepData($goto)){
                        $unsettledPage=$goto;
    
                        break;
                    }
                }

                $index++;
            }


            return $unsettledPage;


        }

        static function unsetAllReviewData(){
            $index=0;
            $cunt=count(StepsClass::$allData);
            while($index < $cunt){
                if(isset($_SESSION[StepsClass::$allData[$index]["data_key"]])){
                    unset($_SESSION[StepsClass::$allData[$index]["data_key"]]);
                }
                $index++;
            }
        }



    }

?>