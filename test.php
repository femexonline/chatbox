<?php
    require_once './resources/backend/root_path.php';

    require_once $ROOT_DIR.'/resources/backend/session-start.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';
    
    $chat=ChatController::getById(1);

    print_r($chat);
