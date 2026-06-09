<?php

class Like {
    private $db;
    private $table = 'likes';

    public function __construct($db) {
        $this->db = $db;
    }

    // Like a post
    public function likePost($user_id, $post_id) {
        // Check if already liked
        if ($this->isLiked($user_id, $post_id)) {
            return false;
        }

        $query = 'INSERT INTO ' . $this->table . '
                  (user_id, post_id, created_at)
                  VALUES
                  (:user_id, :post_id, NOW())';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);

        if ($stmt->execute()) {
            // Update likes count in posts table
            $this->updatePostLikeCount($post_id);
            return true;
        }
        return false;
    }

    // Unlike a post
    public function unlikePost($user_id, $post_id) {
        $query = 'DELETE FROM ' . $this->table . '
                  WHERE user_id = :user_id AND post_id = :post_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);

        if ($stmt->execute()) {
            // Update likes count in posts table
            $this->updatePostLikeCount($post_id);
            return true;
        }
        return false;
    }

    // Check if post is liked
    public function isLiked($user_id, $post_id) {
        $query = 'SELECT id FROM ' . $this->table . '
                  WHERE user_id = :user_id AND post_id = :post_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get likes count
    public function getLikesCount($post_id) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . '
                  WHERE post_id = :post_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Update post likes count
    private function updatePostLikeCount($post_id) {
        $count = $this->getLikesCount($post_id);

        $query = 'UPDATE posts SET likes_count = :count WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':count', $count);
        $stmt->bindParam(':id', $post_id);

        return $stmt->execute();
    }
}

?>