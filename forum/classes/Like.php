<?php
class Like {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function togglePostLike($post_id, $user_id) {
        // Check if already liked
        $query = "SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            // Unlike
            $query = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            return array("action" => "unliked", "success" => true);
        } else {
            // Like
            $query = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {
                return array("action" => "liked", "success" => true);
            }
        }
        return array("success" => false);
    }

    public function toggleCommentLike($comment_id, $user_id) {
        // Check if already liked
        $query = "SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $comment_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            // Unlike
            $query = "DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":comment_id", $comment_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            return array("action" => "unliked", "success" => true);
        } else {
            // Like
            $query = "INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":comment_id", $comment_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {
                return array("action" => "liked", "success" => true);
            }
        }
        return array("success" => false);
    }

    public function toggleReplyLike($reply_id, $user_id) {
        // Check if already liked
        $query = "SELECT id FROM reply_likes WHERE reply_id = :reply_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reply_id", $reply_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            // Unlike
            $query = "DELETE FROM reply_likes WHERE reply_id = :reply_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":reply_id", $reply_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            return array("action" => "unliked", "success" => true);
        } else {
            // Like
            $query = "INSERT INTO reply_likes (reply_id, user_id) VALUES (:reply_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":reply_id", $reply_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {
                return array("action" => "liked", "success" => true);
            }
        }
        return array("success" => false);
    }

    public function isPostLiked($post_id, $user_id) {
        $query = "SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function isCommentLiked($comment_id, $user_id) {
        $query = "SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $comment_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function isReplyLiked($reply_id, $user_id) {
        $query = "SELECT id FROM reply_likes WHERE reply_id = :reply_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reply_id", $reply_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getPostLikeCount($post_id) {
        $query = "SELECT COUNT(*) as count FROM post_likes WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function getCommentLikeCount($comment_id) {
        $query = "SELECT COUNT(*) as count FROM comment_likes WHERE comment_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $comment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function getReplyLikeCount($reply_id) {
        $query = "SELECT COUNT(*) as count FROM reply_likes WHERE reply_id = :reply_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reply_id", $reply_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
?>