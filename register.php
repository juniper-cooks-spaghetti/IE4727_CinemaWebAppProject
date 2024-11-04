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
    <title>Register - CineBox</title>
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
            <h1 class="page-title">Create Account</h1>
            <p class="login-subtitle">Join CineBox to start booking movie tickets!</p>
            <p class="register-prompt">Already have an account? <a href="login.php">Sign in here</a></p>

            <?php if($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="login-box">
                <form id="registerForm" action="process_register.php" method="POST">
                    <div class="form-group">
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" id="phone" name="phone" 
                               placeholder="Phone Number (+65)" 
                               pattern="\+65[0-9]{8}"
                               title="Please enter a valid Singapore phone number (+65 followed by 8 digits)"
                               required>
                    </div>
                    <button type="submit" class="login-button">Create Account</button>
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

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const phone = document.getElementById('phone').value;
        
        // Password matching validation
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        
        // Phone number validation
        const phoneRegex = /^\+65[0-9]{8}$/;
        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Please enter a valid Singapore phone number (+65 followed by 8 digits)');
            return;
        }
    });

    // Auto-format phone number
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value;
        
        // If the input doesn't start with +65, add it
        if (!value.startsWith('+65')) {
            // Remove any existing +65 from anywhere in the string
            value = value.replace('+65', '');
            // Add +65 at the start
            value = '+65' + value;
        }
        
        // Remove any non-digit characters after +65
        value = '+65' + value.substring(3).replace(/[^\d]/g, '');
        
        // Limit to +65 followed by 8 digits
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        e.target.value = value;
    });
    </script>
</body>
</html>