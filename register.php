<?php
    require_once './resources/backend/root_path.php';

    require_once $ROOT_DIR.'/resources/backend/session-start.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';


    $f_name="";
    $l_name="";
    $email="";
    $password="";

    $go_login=false;

    

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $f_name=$_POST["f_name"];
        $l_name=$_POST["l_name"];
        $email=$_POST["email"];
        $password=$_POST["password"];

        if($f_name && $l_name && $email && $password){

            if(!UserController::emailExist($email)){
                $adminId=UserController::addAdmin($f_name, $l_name, $email, $password);
                if($adminId){
                    header("location:login.php");
                    die;
                }

            }

        }

    }

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>Messaging app</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link rel="stylesheet" type="text/css" media='screen' href="./resources/frontend/css/auth.css" />

    </head>
    <body id="messaging_body">

        <nav class="head_nav">
            <ul>
                <a href="">HOME</a>
                <a href="./login.php">Login</a>
                <a href="./register.php">Register</a>
            </ul>
        </nav>

        <div>

            <form method="post">
                <h4>Sign up here</h4>
                <span class="item">
                    <label>First Name</label>
                    <span class="input">
                        <input 
                            type="text" 
                            placeholder="e.g John"
                            name="f_name"
                            value="<?= $f_name ?>"
                        />
                    </span>
                </span>
                <span class="item">
                    <label>Last Name</label>
                    <span class="input">
                        <input 
                            type="text" 
                            placeholder="e.g Deo"
                            name="l_name"
                            value="<?= $l_name ?>"
                        />
                    </span>
                </span>
                <span class="item">
                    <label>Email</label>
                    <span class="input">
                        <input 
                            type="email" 
                            placeholder="john@landscape.com"
                            name="email"
                            value="<?= $email ?>"
                        />
                    </span>
                </span>
                <span class="item">
                    <label>Password</label>
                    <span class="input has_icon">
                        <input 
                            type="password"
                            name="password"
                            placeholder="******" 
                            autocomplete=""
                            value="<?= $password ?>"
                        />
                        <i class="my_icon">eye</i>
                    </span>
                </span>

                <span class="item">
                    <button>Sign up</button>
                </span> 
                
                <!-- <p>
                    Already have an account?
                    <a href="">Signin Here</a>
                </p> -->
            </form>

        </div>

    </body>
    <script>
        //set identification session from backend for security check
    </script>
    <!-- <script src="src/messages.js"></script> -->
    <script src="./resources/frontend/js/auth.js"></script>

</html>