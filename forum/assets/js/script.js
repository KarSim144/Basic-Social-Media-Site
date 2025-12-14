
// Jtüm js fonksiyonları burda
// APIlere istek gönderiyor + cevap alıyor (cagrılanlar burda)


// Sayfa yüklendiğinde çalışır
document.addEventListener('DOMContentLoaded', function() {
    //DOMContentLoaded HTML tamamen yüklendiğinde tetiklenir
    
    const imageInput = document.getElementById('image');
    //create_post.php sayfasındaki <input type="file" id="image"> elementini bulur
    //const değişmez değişken (imageInput değiştirilemez)
    //imageInput bu elementi tutar
    
    if(imageInput) {
        //eğer image input varsa (sadece create_post.php sayfasında var)
        //diğer sayfalarda 0 döner kod çalışmaz
        
        imageInput.addEventListener('change', function(e) {
            //addEventListener olay dinleyici ekler
            //change kullanıcı dosya seçtiğinde tetiklenir
            //function(e) e parametresi olay bilgisini tutar
            
            const preview = document.getElementById('image-preview');
            //create_post.php içindeki <div id="image-preview"></div> elementini bulur
            //önizleme buraya gösterilecek
            
            const file = e.target.files[0];
            //e.target input elementi (dosya seçme kutusu)
            //files[0] seçilen ilk dosya
            //file değişkeni seçilen dosyayı tutar
            
            if(file) {
                //eğer dosya seçildiyse
                
                const reader = new FileReader();
                //FileReader JavaScript objesi (dosyaları okumak için)
                //görsel dosyasını okuyup ekrana gösterebilir
                
                reader.onload = function(e) {
                    //onload dosya okunduktan sonra çalışır
                    //function(e) e.target.result base64 formatında görsel verisi
                    
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 300px; margin-top: 10px;">`;
                    //innerHTML HTML içeriğini değiştirir
                    //backtick ` template literal (değişken içine ${} ile yazılır)
                    //e.target.result görsel datası src'ye konur
                    //Örnek: <img src="data:image/png;base64,iVBORw0K..." alt="Preview">
                    //preview div'ine bu img elementi yerleştirilir kullanıcı görseli görür
                }
                
                reader.readAsDataURL(file);
                //readAsDataURL dosyayı base64 formatına çevirir
                //bu işlem bitince reader.onload tetiklenir
            } else {
                //dosya seçilmediyse
                preview.innerHTML = '';
                //önizlemeyi temizle boş yap
            }
        });
    }
});


function togglePostLike(postId) {
    //togglePostLike fonksiyonu postId parametresi alır
    //HTML'de: <button onclick="togglePostLike(5)">❤</button>
    //postId = 5 hangi gönderiyi beğeneceğiz
    
    fetch('api/like_post.php', {
        //fetch JavaScript AJAX fonksiyonu gönderme
        //'api/like_post.php' hangi dosyaya istek gönderilecek
        
        method: 'POST',
        //POST isteği veri gönderiyor
        
        
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        //headers HTTP başlıkları
        //Content-Type veri formatını belirtir
        
        
        body: 'post_id=' + postId
        //body gönderilen veri
    })
    .then(response => response.json())
    //response API'den gelen yanıt
    //response.json() JSON string'i JavaScript objesine çevirir
    //Örnek: '{"success":true,"action":"liked","like_count":6}' → {success:true, action:"liked", like_count:6}
    
    .then(data => {
      
        if(data.success) {
           
            
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            //querySelector CSS seçici ile element bulur
            //postId = 5 ise bu elementi bulur
            //postCard bu elementi tutar
            
            const likeBtn = postCard.querySelector('.btn-like');
            //postCard içinde .btn-like sınıfına sahip elementi bulur
            //HTML'de: <button class="btn-like">❤</button>
            //likeBtn bu butonu tutar
            
            const likeCount = likeBtn.querySelector('.like-count');
            //likeBtn içinde .like-count sınıfına sahip elementi bulur
            //HTML'de: <span class="like-count">5</span>
            //likeCount bu span'i tutar
            
            if(data.action === 'liked') {
                //eğer beğeni eklendiyse (liked)
                likeBtn.classList.add('liked');
                //classList.add sınıf ekler
                //btn-like elementine liked sınıfı eklenir
                //CSS'de: .btn-like.liked { color: red; } kırmızı olur
            } else {
                //eğer beğeni kaldırıldıysa (unliked)
                likeBtn.classList.remove('liked');
                //classList.remove sınıf kaldırır
                //liked sınıfı kaldırılır buton normal renge döner
            }
            
            likeCount.textContent = data.like_count;
            //textContent elementin metin içeriğini değiştirir
            //data.like_count = 6 ise span içine 6 yazar
            //HTML: <span class="like-count">6</span>
            //kullanıcı güncel beğeni sayısını görür
        }
    })
    .catch(error => console.error('Error:', error));
    //catch hata oluşursa çalışır
    //console.error tarayıcı konsoluna hata yazar
    //Örnek: ağ hatası internet kesilirse
}

function toggleCommentLike(commentId) {
    //toggleCommentLike fonksiyonu commentId parametresi alır
    //HTML'de: <button onclick="toggleCommentLike(10)">❤</button>
    //commentId = 10 hangi yorumu beğeneceğiz
    
    fetch('api/like_comment.php', {
        //like_comment.php API endpoint'ine istek gönderir
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'comment_id=' + commentId
        //commentId = 10 ise body = 'comment_id=10'
        //PHP'de: $_POST['comment_id'] = 10
    })
    .then(response => response.json())
    //JSON yanıtı JavaScript objesine çevir
    
    .then(data => {
        //data = {success:true, action:"liked", like_count:3}
        
        if(data.success) {
            const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
          
            
            const likeBtn = commentCard.querySelector('.btn-like-comment');
       
            
            const likeCount = likeBtn.querySelector('.like-count');
            //beğeni sayısını gösteren span'i bulur
      
            
            if(data.action === 'liked') {
               
                likeBtn.classList.add('liked');
               
            } else {
               
                likeBtn.classList.remove('liked');
                
            }
            
            likeCount.textContent = data.like_count;
            //beğeni sayısını güncelle
            //Örnek: 2 → 3
        }
    })
    .catch(error => console.error('Error:', error));
   
}


function toggleReplyLike(replyId) {
    //toggleReplyLike fonksiyonu replyId parametresi alır
    //HTML'de: <button onclick="toggleReplyLike(15)">❤</button>
    //replyId = 15 hangi cevabı beğeneceğiz
    
    fetch('api/like_reply.php', {
        //like_reply.php API endpoint'ine istek gönderir
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'reply_id=' + replyId
        
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const replyCard = document.querySelector(`[data-reply-id="${replyId}"]`);
            //HTML'de: <div class="reply-card" data-reply-id="15">
            
            const likeBtn = replyCard.querySelector('.btn-like-reply');
           
            
            const likeCount = likeBtn.querySelector('.like-count');
            
            
            if(data.action === 'liked') {
                likeBtn.classList.add('liked');
               
            } else {
                likeBtn.classList.remove('liked');
              
            }
            
            likeCount.textContent = data.like_count;
           
        }
    })
    .catch(error => console.error('Error:', error));
}


function deletePost(postId, redirect = false) {
    //deletePost fonksiyonu iki parametre alır
    //postId hangi gönderi silinecek
    //redirect = false (varsayılan) sayfa yönlendirmesi yapılmasın
    //redirect = true başka sayfaya yönlendir
    //HTML'de: <button onclick="deletePost(5, true)">Sil</button>
    
    if(confirm('Are you sure you want to delete this post?')) {
        //confirm onay kutusu gösterir (Tamam/İptal)
      
        
        fetch('api/delete_post.php', {
            //delete_post.php API endpoint'ine istek gönderir
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
            //postId = 5 ise body = 'post_id=5'
            //PHP'de: $_POST['post_id'] = 5
        })
        .then(response => response.json())
        .then(data => {
            //data = {success:true} veya {success:false}
            
            if(data.success) {
                //silme başarılıysa
                
                if(redirect) {
                    //eğer redirect = true ise
                    window.location.href = 'index.php';
                    //window.location.href sayfayı yönlendirir
                } else {
                
                    const postCard = document.querySelector(`[data-post-id="${postId}"]`);
                    //silinecek gönderi kartını bul
                    postCard.remove();
                    //remove() elementi HTML'den siler
                   
                }
            } else {
               
                alert('Failed to delete post');
                
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
}


function deleteComment(commentId) {
    //deleteComment fonksiyonu commentId parametresi alır
    //HTML'de: <button onclick="deleteComment(25)">Sil</button>
    
    if(confirm('Are you sure you want to delete this comment?')) {
      
        
        fetch('api/delete_comment.php', {
            //delete_comment.php API endpoint'ine istek gönderir
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'comment_id=' + commentId
            //commentId = 25 ise body = 'comment_id=25'
          
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
              
                const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
              
                
                commentCard.remove();
                //yorum kartını HTML'den sil
                //sayfa yenilenmeden yorum kaybolur
            } else {
                alert('Failed to delete comment');
                //başarısızsa uyarı göster
            }
        })
        .catch(error => console.error('Error:', error));
    }
}


function deleteReply(replyId) {
    //deleteReply fonksiyonu replyId parametresi alır
    //HTML'de: <button onclick="deleteReply(30)">Sil</button>
    
    if(confirm('Are you sure you want to delete this reply?')) {
        
        
        fetch('api/delete_reply.php', {
            //delete_reply.php API endpoint'ine istek gönderir
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'reply_id=' + replyId
            //replyId = 30 ise body = 'reply_id=30'
            //PHP'de: $_POST['reply_id'] = 30
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const replyCard = document.querySelector(`[data-reply-id="${replyId}"]`);
                //HTML'de: <div class="reply-card" data-reply-id="30">
                
                replyCard.remove();
                //cevap kartını sil
            } else {
                alert('Failed to delete reply');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}


function toggleReplyForm(commentId) {
    //toggleReplyForm fonksiyonu commentId parametresi alır
    //HTML'de: <button onclick="toggleReplyForm(10)">Cevapla</button>
    //commentId = 10 hangi yoruma cevap vereceğiz
    
    const replyForm = document.getElementById('reply-form-' + commentId);
    //document.getElementById id'ye göre element bulur
    //commentId = 10 ise 'reply-form-10' id'li elementi bulur
    //view_post.php içinde: <div id="reply-form-10" style="display:none;">
    //replyForm bu div'i tutar (içinde textarea ve butonlar var)
    
    if(replyForm.style.display === 'none' || replyForm.style.display === '') {
        //eğer form gizliyse (display:none veya boş)
        //style.display CSS display özelliğini kontrol eder
        //=== tam eşit (none veya boş string)
        //|| VEYA operatörü (ikisinden biri doğruysa)
        
        replyForm.style.display = 'block';
        //display = 'block' formu göster
        //CSS: display:block elementi görünür yapar
        
        replyForm.querySelector('textarea').focus();
        //querySelector form içindeki textarea'yı bulur
        //focus() imleç textarea'ya gelir kullanıcı hemen yazmaya başlayabilir
        //Kullanıcı deneyimi için iyi
    } else {
        //form görünürse
        replyForm.style.display = 'none';
        //display = 'none' formu gizle
        //form kaybolur
    }
    //her tıklamada form açılır/kapanır (toggle = değiştir)
}

//kısacası bütün fonksiyonalr burda (ayrıca api kloserundende cagrılıyor)