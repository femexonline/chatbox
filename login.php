<?php
    require_once './resources/backend/root_path.php';

    require_once $ROOT_DIR.'/resources/backend/session-start.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';


    $email="";
    $password="";

    

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $email=$_POST["email"];
        $password=$_POST["password"];

        if($email && $password){

            $admin=UserController::getAdminByEmail($email);

            if($admin){

                if(password_verify($password, $admin["password"])){
                    $admin_login_id=$admin["id"];
                    echo "
                        <script>
                            const admin_login_id=$admin_login_id;
                        </script>
                    ";
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
                <h4>Login here</h4>
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
                    <button>Login</button>
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