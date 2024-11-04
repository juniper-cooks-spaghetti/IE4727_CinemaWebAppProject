<?php
require_once 'auth.inc.php';
checkLogin();

require_once 'db_config.php';

// Fetch current user data
try {
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT Username, Email, PhoneNumber FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading profile data. Please try again.";
    header("Location: index.php");
    exit();
}

// Check for any error or success messages
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Profile - CineBox</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content">
        <div class="login-container">
            <h1 class="page-title">Edit Profile</h1>
            <p class="login-subtitle">Update your account information</p>

            <?php if($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if($success_message): ?>
                <div class="success-message" style="background: #166534; color: white; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <div class="login-box">
                <form id="editProfileForm" action="update_profile.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($userData['Username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($userData['Email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($userData['PhoneNumber']); ?>"
                               pattern="\+65[0-9]{8}"
                               title="Please enter a valid Singapore phone number (+65 followed by 8 digits)"
                               required>
                        <small style="color: #666; font-size: 0.8em;">Format: +65 followed by 8 digits</small>
                    </div>
                    <div class="form-group">
                        <label for="current_password">Current Password (required to save changes)</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password (leave blank to keep current password)</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password">
                    </div>
                    <button type="submit" class="login-button">Save Changes</button>
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
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmNewPassword = document.getElementById('confirm_new_password').value;
        const phone = document.getElementById('phone').value;
        
        // If new password is provided, check if it matches confirmation
        if (newPassword || confirmNewPassword) {
            if (newPassword !== confirmNewPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return;
            }
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long');
                return;
            }
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
            value = value.replace('+65', '');
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