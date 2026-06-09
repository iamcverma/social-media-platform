<?php

header('Content-Type: application/json');
session_start();

require_once '../config/Database.php';
require_once '../classes/Comment.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->connect();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'] ?? '';
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
        exit;
    }

    $comment = new Comment($db);
    $comment->post_id = $post_id;
    $comment->user_id = $user_id;
    $comment->content = $content;

    if ($comment->addComment()) {
        echo json_encode(['success' => true, 'message' => 'Comment added']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
} elseif ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $post_id = $_GET['post_id'] ?? '';

    $comment = new Comment($db);
    $comments = $comment->getPostComments($post_id);

    echo json_encode(['success' => true, 'comments' => $comments]);
} elseif ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment_id = $_POST['comment_id'] ?? '';
    $post_id = $_POST['post_id'] ?? '';
    $user_id = $_SESSION['user_id'];

    $comment = new Comment($db);
    if ($comment->deleteComment($comment_id, $post_id)) {
        echo json_encode(['success' => true, 'message' => 'Comment deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

?>