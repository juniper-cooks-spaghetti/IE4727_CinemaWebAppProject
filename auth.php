<?php
require_once 'auth.inc.php';
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect_url = $_POST['redirect'] ?? 'index.php';

    // Validate redirect URL (security measure)
    if (!filter_var($redirect_url, FILTER_VALIDATE_URL) && strpos($redirect_url, '/') !== 0) {
        $redirect_url = 'index.php';
    }

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields";
        header("Location: login.php");
        exit();
    }

    try {
        $conn = new mysqli($servername, $username_db, $password_db, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT UserID, Username, Email, PasswordHash FROM Users WHERE Email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $hashed_password = hash('sha256', $password);
            
            if ($hashed_password === $user['PasswordHash']) {
                loginUser($user);
                unset($_SESSION['redirect_after_login']); // Clear the stored redirect URL
                $stmt->close();
                $conn->close();
                header("Location: " . $redirect_url);
                exit();
            }
        }

        $_SESSION['error_message'] = "Invalid email or password";
        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }

    header("Location: login.php");
    exit();
}