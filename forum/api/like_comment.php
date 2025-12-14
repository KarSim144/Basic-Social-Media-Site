<?php
//Alıntılanan dosyalar:
require_once '../config/database.php'; 
//Database sınıfı (MySQL bağlantısı için)
require_once '../classes/Like.php'; 
//Like sınıfı (beğeni işlemleri için - togglePostLike, toggleCommentLike, toggleReplyLike, isPostLiked, getPostLikeCount vb fonksiyonları var)
require_once '../includes/session.php'; 
//Session fonksiyonları (isLoggedIn, getUserId fonksiyonları var)

header('Content-Type: application/json'); //Browser Json formatında


//Giriş kontrolü (yine)
if(!isLoggedIn()) { 
    echo json_encode(array("success" => false, "message" => "Not logged in"));
   
    exit(); 
}

//yine post gelicek
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$_SERVER php özel değişkeni (sunucu bilgileri tutar)
   
    
    $comment_id = $_POST['comment_id'] ?? 0;
    //yine like alcak yorum comment id ile belirlenicek
    
    $database = new Database();
  
    
    
    $db = $database->getConnection();
    //aynı iş
    
    $like = new Like($db);
    //Like sınıfından yeni obje oluştur
    //$db constructor'a bağlantı gönderilir
    //Like.php içinde: public function __construct($db) { $this->conn = $db; }
    //Like değişkeni database kullanabilmesi için db parametresi ile yapıldı
    
    $result = $like->toggleCommentLike($comment_id, getUserId());
    //toggleCommentLike fonksiyonu Like.php içinde tanımlı
    //$comment_id yine aynı sekilde kullanılıyor
    //getUserId() session.php içindeki fonksiyon kullanıcı idsi
    //Bu fonksiyon beğeni varsa kaldırır yoksa ekler (toggle = değiştir)
    //SELECT FROM comment_likes WHERE comment_id = 10 AND user_id = 5 >> buda sql kısmı
    //$result değişkeni bu diziyi tutar (normal dizi)
    
    $like_count = $like->getCommentLikeCount($comment_id);
    //getCommentLikeCount fonksiyonu Like.php içinde tanımlı
    //sayıyı al sonra göster (altta)
    //SELECT COUNT(*) FROM comment_likes WHERE comment_id = 10 bu sql kısmı
    //$like_count = 5 değişken bu
    
    $result['like_count'] = $like_count;
    //$result dizisine yeni bir değer eklenir
    //Şimdi $result: array("action" => "liked", "success" => true, "like_count" => 5)
    //JS hem action (begenme işi) hem de beğeni sayısını alıcak
    
    echo json_encode($result);
    //Json çıktı: {"action":"liked","success":true,"like_count":5}
    //JavaScript bu veriyi alır ve sayfa güncellenir
    //JavaScript: if(data.action === 'liked') { btn.classList.add('liked'); }
    //JavaScript: likeCount.textContent = data.like_count; (5 yazar)
    
} else {
  
    echo json_encode(array("success" => false));
    
}

//Json yanıt gönderildi JavaScript fetch() bunu alacak:
//.then(response => response.json())
//.then(data => { 
//    if(data.action === 'liked') { likeBtn.classList.add('liked'); }
//    else { likeBtn.classList.remove('liked'); }
//    likeCount.textContent = data.like_count;
//})
?>