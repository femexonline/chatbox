<?php
    require_once '../root_path.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';

    // just busy
    // just busy
    // just busy
    // just busy

    $admins=UserController::getAdminsMax2();
    $adminsCount=UserController::getAdminsCount();

    $res=[
        "admins"=>$admins,
        "count"=>$adminsCount
    ];

    echo json_encode($res);
