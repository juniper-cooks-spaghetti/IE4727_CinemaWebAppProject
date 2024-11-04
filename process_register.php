<?php
session_start();
require_once 'db_config.php';

// Create short variable names
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$phone = $_POST['phone'] ?? '';

// Basic validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone)) {
    $_SESSION['error_message'] = "Please fill in all required fields";
    header("Location: register.php");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Invalid email format";
    header("Location: register.php");
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    $_SESSION['error_message'] = "Password must be at least 6 characters long";
    header("Location: register.php");
    exit();
}

// Check if passwords match
if ($password !== $confirm_password) {
    $_SESSION['error_message'] = "Passwords do not match";
    header("Location: register.php");
    exit();
}

// Validate phone number format
if (!preg_match('/^\+65[0-9]{8}$/', $phone)) {
    $_SESSION['error_message'] = "Please enter a valid Singapore phone number (+65 followed by 8 digits)";
    header("Location: register.php");
    exit();
}

try {
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Email already registered";
        header("Location: register.php");
        exit();
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Username already taken";
        header("Location: register.php");
        exit();
    }

    // Check if phone number already exists
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Phone number already registered";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashed_password = hash('sha256', $password);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO Users (Username, Email, PasswordHash, PhoneNumber, CreatedAt, UpdatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $phone);
    
    if ($stmt->execute()) {
        // Set session variables for automatic login
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        // Redirect to home page
        header("Location: index.php");
        exit();
    } else {
        throw new Exception("Error registering user");
    }

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred during registration. Please try again.";
    header("Location: register.php");
    exit();
}