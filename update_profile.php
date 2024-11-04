<?php
session_start();
require_once 'auth.inc.php';
require_once 'db_config.php';

checkLogin();

// Get form data
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_new_password = $_POST['confirm_new_password'] ?? '';

try {
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Verify current password
    $stmt = $conn->prepare("SELECT PasswordHash FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || hash('sha256', $current_password) !== $user['PasswordHash']) {
        $_SESSION['error_message'] = "Current password is incorrect";
        header("Location: edit_profile.php");
        exit();
    }

    // Check if new username is taken (if username was changed)
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Username = ? AND UserID != ?");
    $stmt->bind_param("si", $username, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_message'] = "Username already taken";
        header("Location: edit_profile.php");
        exit();
    }

    // Check if new email is taken (if email was changed)
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ? AND UserID != ?");
    $stmt->bind_param("si", $email, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_message'] = "Email already registered";
        header("Location: edit_profile.php");
        exit();
    }

    // Check if new phone is taken (if phone was changed)
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE PhoneNumber = ? AND UserID != ?");
    $stmt->bind_param("si", $phone, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_message'] = "Phone number already registered";
        header("Location: edit_profile.php");
        exit();
    }

    // Prepare the update query
    $query = "UPDATE Users SET Username = ?, Email = ?, PhoneNumber = ?";
    $params = [$username, $email, $phone];
    $types = "sss";

    // If new password was provided, include it in the update
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $_SESSION['error_message'] = "New password must be at least 6 characters long";
            header("Location: edit_profile.php");
            exit();
        }
        if ($new_password !== $confirm_new_password) {
            $_SESSION['error_message'] = "New passwords do not match";
            header("Location: edit_profile.php");
            exit();
        }
        $query .= ", PasswordHash = ?";
        $params[] = hash('sha256', $new_password);
        $types .= "s";
    }

    $query .= " WHERE UserID = ?";
    $params[] = $_SESSION['user_id'];
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        $_SESSION['success_message'] = "Profile updated successfully";
    } else {
        throw new Exception("Error updating profile");
    }

} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while updating your profile";
}

header("Location: edit_profile.php");
exit();
?>