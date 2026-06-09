<?php

header('Content-Type: application/json');
session_start();

require_once '../config/Database.php';
require_once '../classes/User.php';
require_once '../classes/Follower.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->connect();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'] ?? $_SESSION['user_id'];

    $user = new User($db);
    $user_data = $user->getUserById($user_id);

    if ($user_data) {
        echo json_encode(['success' => true, 'user' => $user_data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} elseif ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $user_id = $_SESSION['user_id'];

    $profile_pic = null;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['size'] > 0) {
        $file = $_FILES['profile_pic'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid image type']);
            exit;
        }

        $filename = 'profile_' . $user_id . '.' . $file_ext;
        $upload_path = '../public/uploads/' . $filename;

        if (!is_dir('../public/uploads/')) {
            mkdir('../public/uploads/', 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $profile_pic = $filename;
        }
    }

    $user = new User($db);
    $user->id = $user_id;
    $user->full_name = $full_name;
    $user->bio = $bio;
    $user->profile_pic = $profile_pic ?? null;

    if ($user->updateProfile()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
} elseif ($action == 'follow' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $following_id = $_POST['following_id'] ?? '';
    $follower_id = $_SESSION['user_id'];

    $follower = new Follower($db);
    if ($follower->followUser($follower_id, $following_id)) {
        echo json_encode(['success' => true, 'message' => 'User followed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Already following']);
    }
} elseif ($action == 'unfollow' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $following_id = $_POST['following_id'] ?? '';
    $follower_id = $_SESSION['user_id'];

    $follower = new Follower($db);
    if ($follower->unfollowUser($follower_id, $following_id)) {
        echo json_encode(['success' => true, 'message' => 'User unfollowed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not following']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

?>