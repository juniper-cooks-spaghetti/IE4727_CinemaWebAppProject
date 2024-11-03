<?php
require_once 'auth.inc.php';

// If user is already logged in, redirect to home page
if(isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Check for any error messages stored in session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - CineBox</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <header>
        <div class="logo">CineBox</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="#catalogue">Catalogue</a>
            <a href="cart.php">My Bookings</a>
        </nav>
        <div class="profile-icon">👤</div>
    </header>
    
    <div class="main-content">
        <div class="login-container">
            <h1 class="page-title">Login</h1>
            <p class="login-subtitle">Welcome back! Sign in to be able to book tickets and see your pending orders.</p>
            <p class="register-prompt">Register for an account if you haven't done so already</p>

            <?php if($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="login-box">
                <form id="loginForm" action="auth.php" method="POST">
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="E-mail" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group remember-me">
                        <label>
                            <input type="checkbox" name="remember_me" id="remember_me">
                            <span>Remember Me</span>
                        </label>
                    </div>
                    <button type="submit" class="login-button">Log in</button>
                    <a href="register.php" class="create-account-button">Create a New Account</a>
                </form>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-section">
            <h3>Contact Us!</h3>
            <p>feedback@cinebox.com</p>
        </div>
        <div class="footer-section">
            <h3>Visit Us!</h3>
            <p>26 Street, 380381 Singapore</p>
        </div>
        <div class="footer-section">
            <p>&copy; 2024 CineBox Singapore</p>
        </div>
    </footer>
</body>
</html>