<?php

header('Content-Type: application/json');
session_start();

require_once '../config/Database.php';
require_once '../config/Config.php';
require_once '../classes/Post.php';
require_once '../classes/Like.php';
require_once '../classes/Comment.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->connect();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Post content is required']);
        exit;
    }

    $image = null;
    $video = null;

    // Handle file uploads
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $file = $_FILES['file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds limit']);
            exit;
        }

        $filename = uniqid() . '.' . $file_ext;
        $upload_path = UPLOAD_DIR . $filename;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            if (in_array($file_ext, ['mp4', 'avi', 'mov'])) {
                $video = $filename;
            } else {
                $image = $filename;
            }
        }
    }

    $post = new Post($db);
    $post->user_id = $user_id;
    $post->content = $content;
    $post->image = $image;
    $post->video = $video;

    if ($post->createPost()) {
        echo json_encode(['success' => true, 'message' => 'Post created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create post']);
    }
} elseif ($action == 'like' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'] ?? '';
    $user_id = $_SESSION['user_id'];

    $like = new Like($db);
    if ($like->likePost($user_id, $post_id)) {
        $count = $like->getLikesCount($post_id);
        echo json_encode(['success' => true, 'likes_count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Already liked']);
    }
} elseif ($action == 'unlike' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'] ?? '';
    $user_id = $_SESSION['user_id'];

    $like = new Like($db);
    if ($like->unlikePost($user_id, $post_id)) {
        $count = $like->getLikesCount($post_id);
        echo json_encode(['success' => true, 'likes_count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not liked']);
    }
} elseif ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'] ?? '';
    $user_id = $_SESSION['user_id'];

    $post = new Post($db);
    $post_data = $post->getPostById($post_id);

    if (!$post_data || ($post_data['user_id'] != $user_id && !$_SESSION['is_admin'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    if ($post->deletePost($post_id)) {
        echo json_encode(['success' => true, 'message' => 'Post deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

?>