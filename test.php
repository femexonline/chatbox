<?php
    require_once './resources/backend/root_path.php';

    require_once $ROOT_DIR.'/resources/backend/session-start.php';

    require_once $ROOT_DIR.'/resources/backend/controllers/ChatController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/UserController.php';
    require_once $ROOT_DIR.'/resources/backend/controllers/MessageController.php';
    
    // UserController::addUser();

    $userid=4;
    $useridentifier="2_17334367102";

    function msgText($maxNumWord = 10) {
        $alphabet = "abcdefghijklmnoopqrstuvwxyz";
        $msg = "";
    
        // Generate a random number of words
        $words = rand(0, $maxNumWord);
    
        for ($i = 0; $i < $words; $i++) {
            $start = rand(0, 23); // Random start index (0-23)
            $length = rand(1, 5); // Random word length (1-5)
            $word = substr($alphabet, $start, $length);
    
            if ($i > 0) {
                $msg .= " " . $word; // Add space before word if not the first word
            } else {
                $msg .= $word; // No space for the first word
            }
        }
    
        return $msg;
    }

    // $index=0;
    // while($index<5){
    //     MessageController::addMessage(4, $userid, msgText());
    //     // MessageController::addFirstChatMessage($useridentifier, $userid, msgText());

    //     $index++;
    // };


    // ChatController::oldestAdminUnreadChatMsgTime(1);

    // $chats=ChatController::getAdminChats(1, 2);
    // foreach($chats as $chat){
    //     print_r("<br>");

    //     echo($chat["id"]);
    //     echo(" - ");
    //     echo($chat["time_sent"]);
    // }

//    var_dump(MessageController::UserHasUnreadMsgInChat(1, 5));

    print(time());

