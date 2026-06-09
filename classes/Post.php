<?php

class Post {
    private $db;
    private $table = 'posts';

    public $id;
    public $user_id;
    public $content;
    public $image;
    public $video;
    public $likes_count;
    public $comments_count;
    public $created_at;

    public function __construct($db) {
        $this->db = $db;
    }

    // Create post
    public function createPost() {
        $query = 'INSERT INTO ' . $this->table . '
                  (user_id, content, image, video, created_at)
                  VALUES
                  (:user_id, :content, :image, :video, NOW())';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':video', $this->video);

        return $stmt->execute();
    }

    // Get user feed
    public function getUserFeed($user_id, $limit = 10, $offset = 0) {
        $query = 'SELECT p.id, p.user_id, p.content, p.image, p.video, p.likes_count, p.comments_count, p.created_at,
                         u.username, u.full_name, u.profile_pic
                  FROM ' . $this->table . ' p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.user_id IN (
                      SELECT following_id FROM followers WHERE follower_id = :user_id
                  ) OR p.user_id = :user_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user posts
    public function getUserPosts($user_id, $limit = 10, $offset = 0) {
        $query = 'SELECT id, user_id, content, image, video, likes_count, comments_count, created_at
                  FROM ' . $this->table . '
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get post by ID
    public function getPostById($id) {
        $query = 'SELECT p.id, p.user_id, p.content, p.image, p.video, p.likes_count, p.comments_count, p.created_at,
                         u.username, u.full_name, u.profile_pic
                  FROM ' . $this->table . ' p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete post
    public function deletePost($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Get all posts (for admin)
    public function getAllPosts($limit = 20, $offset = 0) {
        $query = 'SELECT p.id, p.user_id, p.content, p.image, p.video, p.likes_count, p.comments_count, p.created_at,
                         u.username, u.full_name
                  FROM ' . $this->table . ' p
                  JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>