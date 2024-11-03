<?php
session_start();
// Clear all session variables
session_unset();
// Destroy the session
session_destroy();
// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
// Redirect to login page
header("Location: login.php");
exit();