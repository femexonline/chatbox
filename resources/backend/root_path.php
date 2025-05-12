<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $BAIT="/chat_box/chat_box";
    // $BAIT="";
    // $BAIT="";
    // $BAIT="";
    // $BAIT="";
    // $BAIT="";

    $ROOT_DIR=$_SERVER["DOCUMENT_ROOT"].$BAIT;
    $ROOT_ADDR=$BAIT;
    $ROOT_ADDR2="/".$_SERVER["SERVER_NAME"].$BAIT;
    $ROOT_ADDR_FULL=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].$ROOT_ADDR;
    
    
    $ROOT_DIR=str_replace("%20", " " ,$ROOT_DIR);




    // print_r($ROOT_ADDR);
    // echo "<br/>";
    // print_r($ROOT_ADDR2);
    // echo "<br/>";
    // print_r($ROOT_ADDR_FULL);
?>