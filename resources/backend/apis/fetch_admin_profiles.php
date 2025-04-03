<?php
    require_once '../root_path.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';

    $admins=UserController::getAdminsMax2();
    $adminsCount=UserController::getAdminsCount();

    $res=[
        "admins"=>$admins,
        "count"=>$adminsCount
    ];

    echo json_encode($res);
