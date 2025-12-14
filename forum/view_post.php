<?php
require_once 'config/database.php';
require_once 'classes/Post.php';
require_once 'classes/Comment.php';
require_once 'classes/Reply.php';
require_once 'classes/Like.php';
require_once 'includes/session.php';

requireLogin();
//yorumları görme yeri loop ve paragraf kullanarak gösteriliyor
$post_id = $_GET['id'] ?? 0;

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);
$comment = new Comment($db);
$reply = new Reply($db);
$like = new Like($db);

$post->id = $post_id;
$post_data = $post->readOne();

if(!$post_data) {
    header("Location: index.php");
    exit();
}

// Handle comment submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_content'])) {
    $comment->post_id = $post_id;
    $comment->user_id = getUserId();
    $comment->content = $_POST['comment_content'];
    $comment->create();
    header("Location: view_post.php?id=" . $post_id);
    exit();
}

// Handle reply submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_content']) && isset($_POST['comment_id'])) {
    $reply->comment_id = $_POST['comment_id'];
    $reply->user_id = getUserId();
    $reply->content = $_POST['reply_content'];
    $reply->create();
    header("Location: view_post.php?id=" . $post_id);
    exit();
}

$comment->post_id = $post_id;
$comments_stmt = $comment->readByPost();
$isPostLiked = $like->isPostLiked($post_id, getUserId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post_data['title']); ?> - Forum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="post-detail">
            <div class="post-card">
                <div class="post-header">
                    <div class="post-author">
                        <strong><?php echo htmlspecialchars($post_data['username']); ?></strong>
                        <span class="post-date"><?php echo date('M d, Y H:i', strtotime($post_data['created_at'])); ?></span>
                    </div>
                    <?php if($post_data['user_id'] == getUserId()): ?>
                        <button class="btn-delete" onclick="deletePost(<?php echo $post_data['id']; ?>, true)">Delete</button>
                    <?php endif; ?>
                </div>

                <h1 class="post-title"><?php echo htmlspecialchars($post_data['title']); ?></h1>
                <p class="post-content"><?php echo nl2br(htmlspecialchars($post_data['content'])); ?></p>

                <?php if($post_data['image_path']): ?>
                    <div class="post-image">
                        <img src="<?php echo htmlspecialchars($post_data['image_path']); ?>" alt="Post image">
                    </div>
                <?php endif; ?>

                <div class="post-actions">
                    <button class="btn-like <?php echo $isPostLiked ? 'liked' : ''; ?>" 
                            onclick="togglePostLike(<?php echo $post_data['id']; ?>)">
                        <span class="like-icon">❤</span>
                        <span class="like-count"><?php echo $post_data['like_count']; ?></span> Likes
                    </button>
                </div>
            </div>

            <div class="comments-section">
                <h2>Comments</h2>

                <form method="POST" action="" class="comment-form">
                    <textarea name="comment_content" placeholder="Write a comment..." rows="3" required></textarea>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>

                <div class="comments-list">
                    <?php while($comment_row = $comments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php 
                        $isCommentLiked = $like->isCommentLiked($comment_row['id'], getUserId());
                        
                        // Get replies for this comment
                        $reply->comment_id = $comment_row['id'];
                        $replies_stmt = $reply->readByComment();
                        $reply_count = $reply->getReplyCountForComment($comment_row['id']);
                        ?>
                        <div class="comment-card" data-comment-id="<?php echo $comment_row['id']; ?>">
                            <div class="comment-header">
                                <div>
                                    <strong><?php echo htmlspecialchars($comment_row['username']); ?></strong>
                                    <span class="comment-date"><?php echo date('M d, Y H:i', strtotime($comment_row['created_at'])); ?></span>
                                </div>
                            </div>
                            <p class="comment-content"><?php echo nl2br(htmlspecialchars($comment_row['content'])); ?></p>
                            <div class="comment-actions">
                                <button class="btn-like-comment <?php echo $isCommentLiked ? 'liked' : ''; ?>" 
                                        onclick="toggleCommentLike(<?php echo $comment_row['id']; ?>)">
                                    <span class="like-icon">❤</span>
                                    <span class="like-count"><?php echo $comment_row['like_count']; ?></span>
                                </button>
                                <button class="btn-reply" onclick="toggleReplyForm(<?php echo $comment_row['id']; ?>)">
                                    💬 Reply (<?php echo $reply_count; ?>)
                                </button>
                                <?php if($comment_row['user_id'] == getUserId()): ?>
                                    <button class="btn-delete-small" onclick="deleteComment(<?php echo $comment_row['id']; ?>)">Delete</button>
                                <?php endif; ?>
                            </div>

                            <!-- Reply Form -->
                            <div class="reply-form-container" id="reply-form-<?php echo $comment_row['id']; ?>" style="display: none;">
                                <form method="POST" action="" class="reply-form">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment_row['id']; ?>">
                                    <textarea name="reply_content" placeholder="Write a reply..." rows="2" required></textarea>
                                    <button type="submit" class="btn btn-primary btn-small">Post Reply</button>
                                    <button type="button" class="btn btn-secondary btn-small" onclick="toggleReplyForm(<?php echo $comment_row['id']; ?>)">Cancel</button>
                                </form>
                            </div>

                            <!-- Replies List -->
                            <?php if($reply_count > 0): ?>
                                <div class="replies-list">
                                    <?php while($reply_row = $replies_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                        <?php $isReplyLiked = $like->isReplyLiked($reply_row['id'], getUserId()); ?>
                                        <div class="reply-card" data-reply-id="<?php echo $reply_row['id']; ?>">
                                            <div class="reply-header">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($reply_row['username']); ?></strong>
                                                    <span class="reply-date"><?php echo date('M d, Y H:i', strtotime($reply_row['created_at'])); ?></span>
                                                </div>
                                            </div>
                                            <p class="reply-content"><?php echo nl2br(htmlspecialchars($reply_row['content'])); ?></p>
                                            <div class="reply-actions">
                                                <button class="btn-like-reply <?php echo $isReplyLiked ? 'liked' : ''; ?>" 
                                                        onclick="toggleReplyLike(<?php echo $reply_row['id']; ?>)">
                                                    <span class="like-icon">❤</span>
                                                    <span class="like-count"><?php echo $reply_row['like_count']; ?></span>
                                                </button>
                                                <?php if($reply_row['user_id'] == getUserId()): ?>
                                                    <button class="btn-delete-small" onclick="deleteReply(<?php echo $reply_row['id']; ?>)">Delete</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>