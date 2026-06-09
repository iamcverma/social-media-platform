<?php

class User {
    private $db;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $bio;
    public $profile_pic;
    public $created_at;

    public function __construct($db) {
        $this->db = $db;
    }

    // Register new user
    public function register() {
        $query = 'INSERT INTO ' . $this->table . '
                  (username, email, password, full_name, created_at)
                  VALUES
                  (:username, :email, :password, :full_name, NOW())';

        $stmt = $this->db->prepare($query);

        // Hash password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':full_name', $this->full_name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login user
    public function login() {
        $query = 'SELECT id, username, email, password, full_name, profile_pic, is_admin
                  FROM ' . $this->table . '
                  WHERE email = :email';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        $query = 'SELECT id, username, email, full_name, bio, profile_pic, followers_count, following_count, posts_count, created_at
                  FROM ' . $this->table . '
                  WHERE id = :id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user profile
    public function updateProfile() {
        $query = 'UPDATE ' . $this->table . '
                  SET full_name = :full_name,
                      bio = :bio,
                      profile_pic = :profile_pic
                  WHERE id = :id';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':profile_pic', $this->profile_pic);

        return $stmt->execute();
    }

    // Check if user exists
    public function userExists($email) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE email = :email';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get all users
    public function getAllUsers($limit = 10, $offset = 0) {
        $query = 'SELECT id, username, email, full_name, profile_pic, created_at
                  FROM ' . $this->table . '
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete user
    public function deleteUser($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}

?>