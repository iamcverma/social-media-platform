<?php

class Follower {
    private $db;
    private $table = 'followers';

    public function __construct($db) {
        $this->db = $db;
    }

    // Follow user
    public function followUser($follower_id, $following_id) {
        if ($follower_id == $following_id) {
            return false;
        }

        // Check if already following
        if ($this->isFollowing($follower_id, $following_id)) {
            return false;
        }

        $query = 'INSERT INTO ' . $this->table . '
                  (follower_id, following_id, created_at)
                  VALUES
                  (:follower_id, :following_id, NOW())';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':follower_id', $follower_id);
        $stmt->bindParam(':following_id', $following_id);

        return $stmt->execute();
    }

    // Unfollow user
    public function unfollowUser($follower_id, $following_id) {
        $query = 'DELETE FROM ' . $this->table . '
                  WHERE follower_id = :follower_id AND following_id = :following_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':follower_id', $follower_id);
        $stmt->bindParam(':following_id', $following_id);

        return $stmt->execute();
    }

    // Check if following
    public function isFollowing($follower_id, $following_id) {
        $query = 'SELECT id FROM ' . $this->table . '
                  WHERE follower_id = :follower_id AND following_id = :following_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':follower_id', $follower_id);
        $stmt->bindParam(':following_id', $following_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get followers
    public function getFollowers($user_id) {
        $query = 'SELECT u.id, u.username, u.full_name, u.profile_pic
                  FROM users u
                  JOIN ' . $this->table . ' f ON u.id = f.follower_id
                  WHERE f.following_id = :user_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get following
    public function getFollowing($user_id) {
        $query = 'SELECT u.id, u.username, u.full_name, u.profile_pic
                  FROM users u
                  JOIN ' . $this->table . ' f ON u.id = f.following_id
                  WHERE f.follower_id = :user_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get followers count
    public function getFollowersCount($user_id) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . '
                  WHERE following_id = :user_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Get following count
    public function getFollowingCount($user_id) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . '
                  WHERE follower_id = :user_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}

?>