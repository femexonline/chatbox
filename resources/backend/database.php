<?php

    //defining variables for connection
    //defining variables for connection
    //defining variables for connection
    //defining variables for connection
    //defining variables for connection
    //defining variables for connection
    $host="localhost";
    $dbname="chat_box";
    $dbuname= "admin";
    $dbpass="Adminadmin9";

    //trying connection in a try... catch block
    try {
        $conn= new PDO("mysql:host=$host; dbname=$dbname;", $dbuname, $dbpass);
    } catch (PDOException $th) {
        die("fialed to connect to user database: ".$th->getMessage());
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

?>