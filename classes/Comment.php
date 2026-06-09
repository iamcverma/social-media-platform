<?php

class Comment {
    private $db;
    private $table = 'comments';

    public $id;
    public $post_id;
    public $user_id;
    public $content;
    public $created_at;

    public function __construct($db) {
        $this->db = $db;
    }

    // Add comment
    public function addComment() {
        $query = 'INSERT INTO ' . $this->table . '
                  (post_id, user_id, content, created_at)
                  VALUES
                  (:post_id, :user_id, :content, NOW())';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            // Update comments count
            $this->updateCommentCount($this->post_id);
            return true;
        }
        return false;
    }

    // Get comments for post
    public function getPostComments($post_id) {
        $query = 'SELECT c.id, c.content, c.created_at, u.username, u.profile_pic, u.id as user_id
                  FROM ' . $this->table . ' c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at DESC';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete comment
    public function deleteComment($id, $post_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $this->updateCommentCount($post_id);
            return true;
        }
        return false;
    }

    // Update comment count
    private function updateCommentCount($post_id) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE post_id = :post_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'];

        $query = 'UPDATE posts SET comments_count = :count WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':count', $count);
        $stmt->bindParam(':id', $post_id);

        return $stmt->execute();
    }
}

?>