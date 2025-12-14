<?php
class Reply {
    private $conn;
    private $table_name = "replies";

    public $id;
    public $comment_id;
    public $user_id;
    public $content;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (comment_id, user_id, content) VALUES (:comment_id, :user_id, :content)";
        $stmt = $this->conn->prepare($query);

        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(":comment_id", $this->comment_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readByComment() {
        $query = "SELECT r.*, u.username,
                  (SELECT COUNT(*) FROM reply_likes WHERE reply_id = r.id) as like_count
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.comment_id = :comment_id
                  ORDER BY r.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $this->comment_id);
        $stmt->execute();
        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getReplyCountForComment($comment_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE comment_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $comment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
?>