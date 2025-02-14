<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    //start session
    session_start();


    //this block of code is used by the logout to return user to previous page after logout
    //it stores the current age in a session
    $_SESSION['CP']=htmlspecialchars($_SERVER['PHP_SELF']);
?>