<?php

header('Content-Type: application/json');
session_start();

require_once '../config/Database.php';
require_once '../config/Config.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit;
        }

        $user = new User($db);

        if ($user->userExists($email)) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->full_name = $full_name;

        if ($user->register()) {
            echo json_encode(['success' => true, 'message' => 'Registration successful. Please login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
    } elseif ($action == 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit;
        }

        $user = new User($db);
        $user->email = $email;
        $user->password = $password;

        $result = $user->login();
        if ($result) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['is_admin'] = $result['is_admin'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $result['is_admin'] ? 'admin/dashboard.php' : 'feed.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } elseif ($action == 'logout') {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>