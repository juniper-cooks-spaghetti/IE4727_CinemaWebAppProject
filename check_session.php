<?php
if (!isset($_SESSION['isSessionStarted'])) {
    session_start();
    $_SESSION['isSessionStarted'] = true;
}
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please log in to access this page";
        header("Location: login.php");
        exit();
    }
    return true;
}

function logout() {
    session_start();
    session_unset();
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    header("Location: login.php");
    exit();
}