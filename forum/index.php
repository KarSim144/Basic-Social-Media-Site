<?php
require_once 'config/database.php';
require_once 'classes/Post.php';
require_once 'classes/Like.php';
require_once 'includes/session.php';

requireLogin();
//butonlar vb burda
$database = new Database();
$db = $database->getConnection();
$post = new Post($db);
$like = new Like($db);

$stmt = $post->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="main-content">
            <div class="create-post-section">
                <h2>Share Something</h2>
                <a href="create_post.php" class="btn btn-primary">Create New Post</a>
            </div>

            <div class="posts-feed">
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php 
                    $isLiked = $like->isPostLiked($row['id'], getUserId());
                    ?>
                    <div class="post-card" data-post-id="<?php echo $row['id']; ?>">
                        <div class="post-header">
                            <div class="post-author">
                                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                                <span class="post-date"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></span>
                            </div>
                            <?php if($row['user_id'] == getUserId()): ?>
                                <button class="btn-delete" onclick="deletePost(<?php echo $row['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </div>

                        <h3 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="post-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                        <?php if($row['image_path']): ?>
                            <div class="post-image">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Post image">
                            </div>
                        <?php endif; ?>

                        <div class="post-actions">
                            <button class="btn-like <?php echo $isLiked ? 'liked' : ''; ?>" 
                                    onclick="togglePostLike(<?php echo $row['id']; ?>)">
                                <span class="like-icon">❤</span>
                                <span class="like-count"><?php echo $row['like_count']; ?></span> Likes
                            </button>
                            <a href="view_post.php?id=<?php echo $row['id']; ?>" class="btn-comment">
                                💬 <?php echo $row['comment_count']; ?> Comments
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>