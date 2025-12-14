<?php
//uyarlanmış
require_once '../config/database.php';
require_once '../classes/Like.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(array("success" => false, "message" => "Not logged in"));
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'] ?? 0;
    
    $database = new Database();
    $db = $database->getConnection();
    $like = new Like($db);
    
    $result = $like->togglePostLike($post_id, getUserId());
    $like_count = $like->getPostLikeCount($post_id);
    
    $result['like_count'] = $like_count;
    echo json_encode($result);
} else {
    echo json_encode(array("success" => false));
}
?>