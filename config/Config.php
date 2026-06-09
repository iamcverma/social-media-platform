<?php

// Site Configuration
define('SITE_URL', 'http://localhost/social-media-platform/public');
define('SITE_NAME', 'SocialHub');
define('UPLOAD_DIR', '../public/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov']);

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('POSTS_PER_PAGE', 10);
define('USERS_PER_PAGE', 20);

?>