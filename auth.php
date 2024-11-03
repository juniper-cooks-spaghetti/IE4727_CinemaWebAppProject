<?php
// Enable error reporting at the top of auth.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For debugging, let's log the POST data
    error_log("POST data: " . print_r($_POST, true));
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields";
        header("Location: login.php");
        exit();
    }

    try {
        $conn = new mysqli($servername, $username, $password_db, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT UserID, Username, Email, PasswordHash FROM Users WHERE Email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password using SHA256
            $hashed_password = hash('sha256', $password);
            error_log("Comparing passwords:");
            error_log("Input hash: " . $hashed_password);
            error_log("Stored hash: " . $user['PasswordHash']);
            
            if ($hashed_password === $user['PasswordHash']) {
                // Set session variables
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['email'] = $user['Email'];

                // Close database resources before redirect
                $stmt->close();
                $conn->close();

                // Log successful login
                error_log("Login successful for user: " . $user['Username']);
                
                // Simple redirect
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Invalid email or password";
                error_log("Password verification failed");
            }
        } else {
            $_SESSION['error_message'] = "Invalid email or password";
            error_log("No user found with email: " . $email);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }

    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}