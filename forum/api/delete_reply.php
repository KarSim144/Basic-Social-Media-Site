<?php
require_once '../config/database.php';
require_once '../classes/Reply.php';
require_once '../includes/session.php';
//Buda reply için özel ayarlanmış delete ajax endpointi
//Reply farkı tabloda cunku yorumlar karışıyor
//Ondan sadece reply için bir api var
header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(array("success" => false, "message" => "Not logged in"));
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply_id = $_POST['reply_id'] ?? 0;
    
    $database = new Database();
    $db = $database->getConnection();
    $reply = new Reply($db);
    
    $reply->id = $reply_id;
    $reply->user_id = getUserId();
    
    if($reply->delete()) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "message" => "Failed to delete"));
    }
} else {
    echo json_encode(array("success" => false));
}
?>