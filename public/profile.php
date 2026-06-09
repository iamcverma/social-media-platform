<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/Database.php';
require_once '../classes/User.php';
require_once '../classes/Post.php';
require_once '../classes/Follower.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);
$post = new Post($db);
$follower = new Follower($db);

$user_id = $_GET['user_id'] ?? $_SESSION['user_id'];
$profile_user = $user->getUserById($user_id);

if (!$profile_user) {
    header('Location: feed.php');
    exit;
}

$user_posts = $post->getUserPosts($user_id, 20, 0);
$is_following = $follower->isFollowing($_SESSION['user_id'], $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile_user['full_name']) ?> - SocialHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="feed.php" class="header-logo">SocialHub</a>
            <nav class="header-nav">
                <a href="feed.php">Home</a>
                <a href="explore.php">Explore</a>
                <a href="profile.php">Profile</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <aside class="sidebar"></aside>

        <main style="grid-column: 2; grid-row: 1;">
            <div class="feed">
                <!-- Profile Header -->
                <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div style="font-size: 32px; margin-bottom: 10px;"><?= substr($profile_user['full_name'], 0, 1) ?></div>
                            <h1 style="margin-bottom: 5px;"><?= htmlspecialchars($profile_user['full_name']) ?></h1>
                            <p style="color: #657786;">@<?= htmlspecialchars($profile_user['username']) ?></p>
                            <p style="margin-top: 10px;"><?= htmlspecialchars($profile_user['bio'] ?? '') ?></p>
                            <div style="display: flex; gap: 20px; margin-top: 15px; color: #657786; font-size: 14px;">
                                <span>Followers: <?= $profile_user['followers_count'] ?></span>
                                <span>Following: <?= $profile_user['following_count'] ?></span>
                                <span>Posts: <?= $profile_user['posts_count'] ?></span>
                            </div>
                        </div>
                        <?php if ($user_id !== $_SESSION['user_id']): ?>
                            <button class="btn btn-primary" style="width: auto; padding: 8px 20px;" onclick="toggleFollow(<?= $user_id ?>)">
                                <?= $is_following ? 'Following' : 'Follow' ?>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary" style="width: auto; padding: 8px 20px;" onclick="editProfile()">Edit Profile</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User Posts -->
                <div>
                    <?php if ($user_posts): ?>
                        <?php foreach ($user_posts as $p): ?>
                            <div class="post">
                                <div class="post-header">
                                    <div class="post-avatar"><?= substr($profile_user['full_name'], 0, 1) ?></div>
                                    <div class="post-info">
                                        <div>
                                            <span class="post-author"><?= htmlspecialchars($profile_user['full_name']) ?></span>
                                            <span class="post-handle">@<?= htmlspecialchars($profile_user['username']) ?></span>
                                        </div>
                                        <div class="post-time"><?= date('M d, Y', strtotime($p['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="post-content"><?= htmlspecialchars($p['content']) ?></div>
                                <?php if ($p['image']): ?>
                                    <div class="post-media">
                                        <img src="uploads/<?= $p['image'] ?>" alt="Post image">
                                    </div>
                                <?php endif; ?>
                                <?php if ($p['video']): ?>
                                    <div class="post-media">
                                        <video controls>
                                            <source src="uploads/<?= $p['video'] ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding: 40px; text-align: center; color: #657786;">
                            <p>No posts yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <aside class="sidebar"></aside>
    </div>

    <div id="message" class="message"></div>

    <script>
    function toggleFollow(userId) {
        const action = this.textContent === 'Follow' ? 'follow' : 'unfollow';
        const btn = event.target;

        const formData = new FormData();
        formData.append('action', action);
        formData.append('following_id', userId);

        fetch('../api/users.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = action === 'follow' ? 'Following' : 'Follow';
                location.reload();
            }
        });
    }

    function editProfile() {
        alert('Edit profile feature coming soon!');
    }
    </script>
</body>
</html>