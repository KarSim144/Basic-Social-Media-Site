<?php
//Alıntılanan dosyalar:
require_once '../config/database.php'; //Database sınıfı (MySQL bağlantısı için)
require_once '../classes/Comment.php'; //Comment sınıfı (yorum işlemleri için - create, delete, readByPost fonksiyonları var)
require_once '../includes/session.php'; //Session fonksiyonları (isLoggedIn, getUserId fonksiyonları var)

header('Content-Type: application/json'); //Browser Json formatında alır (html değil!)
//JavaScript fetch() Json verisi bekler html değil


//Giriş kontrolü (session.php içindeki isLoggedIn fonksiyonu):
if(!isLoggedIn()) { 
    echo json_encode(array("success" => false, "message" => "Not logged in"));
    //json_encode phpyi json formatına çevirir: array("success" => false) → {"success":false}
    exit(); //durdur aşağıdaki kodlar çalışmasın
}
//giriş yapıldı devam eder

//JavaScript fetch()ten post verisi gelirse:
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$_SERVER php değişkeni (sunucu bilgileri tutar)
    //REQUEST_METHOD istek nasıl yapıldı (GET POST falan)
    //JavaScript: fetch('api/delete_comment.php', {method: 'POST'})
    
    $comment_id = $_POST['comment_id'] ?? 0; 
    //$_POST post ile gönderilen veriler (JavaScript body'sinden gelir)
    //JavaScript: body: 'comment_id=25' → $_POST['comment_id'] = 25
    //comment_id yoksa 0 atanır (hata vermemesi için)
    //Örnek gelen veri varsa comment_id = 25  yoksa comment_id = 0 oda tabloda boş ztn

    $database = new Database();
    //yeni obje
    //Database config/database.php içindeki Database sınıfını kullan
    //Objeyi tutacak değişken database
    
    $db = $database->getConnection();
    //getConnection fonksiyonu database.php içinde şuan
    //Bu fonksiyon mysql bağlantısını döndürsün diye var
    //bağlantıyı tutan değişken db

    $comment = new Comment($db);
    //Comment sınıfından yeni obje 
    //$db  bağlantı gönderilir
    //Comment değişkeni database kullanabilmesi için db parametresi ile yapıldı
    
    $comment->id = $comment_id;
    //Comment objesinin id özelliğine değer atanır
    //Bu id DELETE sorgusunda WHERE id = ? kısmında kullanılacak (altta)
    //idye göre silme işlemi yapılsın (daha kolay)
    
    $comment->user_id = getUserId();
    //getUserId session.php içindeki fonksiyon
    //$_SESSION['user_id'] değerini döndürür (giriş yapan kullanıcının idsi)
    //Örnek: giriş yapan kullanıcı ID=5 ise → $comment->user_id = 5
    //fonksiyonla kullanıcı idsi alınır buda sadece kendi yorumunu silmen için var
    //SQL: DELETE FROM comments WHERE id = 25 AND user_id = 5
    //yorum başkasına aitse user_id farklı olur silmez

    if($comment->delete()) {
        //delete() metodu Comment.php içinde tanımlı
        //sorgu burda:  DELETE FROM comments WHERE id = :id AND user_id = :user_id
        //başarılıysa true döner başarısızsa false
        
        echo json_encode(array("success" => true));
        //JavaScript: data.success === true → yorumu sayfadan sil
        
    } else {
        //olmadı
       
        echo json_encode(array("success" => false, "message" => "Failed to delete"));
        //Json çıktısı yine (mesaj)
    }
    
} else {
    //POST değilse (örneğin GET ile erişilmeye çalışıldıysa)
    echo json_encode(array("success" => false));
    //Json çıktı olmadı
}

//Json yanıt gönderildi JavaScript fetch() bunu alacak
?>