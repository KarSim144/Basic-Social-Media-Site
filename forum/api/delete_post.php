<?php
//Bu delete comment pphnin tamamen aynısı sadece post için az degişik 
//Alıntılanan dosyalar:
require_once '../config/database.php'; 
//Database sınıfı (MySQL bağlantısı için)
require_once '../classes/Post.php'; 
//Post sınıfı (gönderi işlemleri için - create, delete, readAll, readOne fonksiyonları var)
require_once '../includes/session.php'; 
//Session fonksiyonları (isLoggedIn, getUserId fonksiyonları var)

header('Content-Type: application/json'); 




if(!isLoggedIn()) { 
    echo json_encode(array("success" => false, "message" => "Not logged in"));
    
    exit(); 
}
//buraya geldiyse giriş yapılmış demektir


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$_SERVER php özel değişkeni (sunucu bilgileri tutar)
   
    
    $post_id = $_POST['post_id'] ?? 0;
  
    
    $database = new Database();
  
    
    $db = $database->getConnection();
 

    $post = new Post($db);
 
    $post->id = $post_id;
  
    $post->user_id = getUserId();

    
    if($post->delete()) {
        //delete() metodu Post.php içinde tanımlı
        //SQL çalıştırır: DELETE FROM posts WHERE id = :id AND user_id = :user_id
        //başarılıysa true döner başarısızsa false döner
        
        echo json_encode(array("success" => true));
        //Json çıktı: {"success":true}
        //JavaScript: data.success === true → gönderiyi sayfadan sil
        
    } else {
        
      
        echo json_encode(array("success" => false, "message" => "Failed to delete"));
        
    }
} else {
 
    echo json_encode(array("success" => false));
   
}

//Json yanıt gönderildi JavaScript fetch() bunu alacak
?>