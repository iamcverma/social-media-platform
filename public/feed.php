<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/Database.php';
require_once '../classes/Post.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->connect();

$post = new Post($db);
$user = new User($db);

$current_user = $user->getUserById($_SESSION['user_id']);
$feed_posts = $post->getUserFeed($_SESSION['user_id'], 10, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - SocialHub</title>
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
                <div class="user-menu">
                    <button class="user-menu-btn" onclick="toggleMenu()">☰</button>
                    <div class="dropdown-menu" id="userMenu">
                        <a href="profile.php">My Profile</a>
                        <button onclick="logout()">Logout</button>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-box">
                <h3>Navigation</h3>
                <a href="feed.php">🏠 Home</a>
                <a href="explore.php">🔍 Explore</a>
                <a href="profile.php">👤 Profile</a>
            </div>
        </aside>

        <!-- Feed -->
        <main class="feed">
            <!-- Compose Box -->
            <div class="compose-box">
                <h3>What's happening?!</h3>
                <form id="postForm" enctype="multipart/form-data">
                    <textarea id="postContent" name="content" placeholder="Share your thoughts..."></textarea>
                    <div class="compose-actions">
                        <div class="compose-icons">
                            <button type="button" onclick="document.getElementById('fileInput').click()">🖼️</button>
                            <input type="file" id="fileInput" style="display: none;" name="file" onchange="handleFileSelect()">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: auto; padding: 8px 20px;">Post</button>
                    </div>
                    <div id="fileName" style="margin-top: 10px; color: #657786; font-size: 14px;"></div>
                </form>
            </div>

            <!-- Posts Feed -->
            <div id="postsFeed">
                <?php foreach ($feed_posts as $p): ?>
                    <div class="post" data-post-id="<?= $p['id'] ?>">
                        <div class="post-header">
                            <div class="post-avatar"><?= substr($p['full_name'], 0, 1) ?></div>
                            <div class="post-info">
                                <div>
                                    <a href="profile.php?user_id=<?= $p['user_id'] ?>" style="text-decoration: none;">
                                        <span class="post-author"><?= htmlspecialchars($p['full_name']) ?></span>
                                        <span class="post-handle">@<?= htmlspecialchars($p['username']) ?></span>
                                    </a>
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
                        <div class="post-actions">
                            <button class="post-action like-btn" onclick="toggleLike(<?= $p['id'] ?>)" data-post-id="<?= $p['id'] ?>">❤️ <span class="like-count"><?= $p['likes_count'] ?></span></button>
                            <button class="post-action" onclick="showComments(<?= $p['id'] ?>)">💬 <span><?= $p['comments_count'] ?></span></button>
                            <?php if ($p['user_id'] == $_SESSION['user_id']): ?>
                                <button class="post-action" onclick="deletePost(<?= $p['id'] ?>)">🗑️ Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Right Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-box">
                <h3>Trending</h3>
                <p style="color: #657786;">No trending topics yet</p>
            </div>
        </aside>
    </div>

    <div id="message" class="message"></div>

    <script src="js/feed.js"></script>
</body>
</html>