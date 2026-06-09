<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialHub - Connect with Friends</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-wrapper">
            <div class="auth-box">
                <h1 class="logo">SocialHub</h1>
                <p class="tagline">Connect with friends and family</p>

                <!-- Login Form -->
                <div id="login-form" class="auth-form active">
                    <h2>Login</h2>
                    <form id="loginForm">
                        <input type="hidden" name="action" value="login">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <p class="form-footer">Don't have an account? <a href="#" onclick="toggleForms()">Register</a></p>
                    </form>
                </div>

                <!-- Register Form -->
                <div id="register-form" class="auth-form">
                    <h2>Create Account</h2>
                    <form id="registerForm">
                        <input type="hidden" name="action" value="register">
                        <div class="form-group">
                            <input type="text" name="full_name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <p class="form-footer">Already have an account? <a href="#" onclick="toggleForms()">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="message" class="message"></div>

    <script src="js/auth.js"></script>
</body>
</html>