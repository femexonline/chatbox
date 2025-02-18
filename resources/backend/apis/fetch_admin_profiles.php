<?php
    require_once '../root_path.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';


    $user=UserController::getAllAdmins();

    echo json_encode($user);
