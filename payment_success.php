<?php
// payment_success.php
require_once 'auth.inc.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBox</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content payment-result">
        <h2 class="result-title">Payment Successful</h2>
        <p class="result-message">Please check your e-mail for your ticket(s)</p>
        
        <div class="result-icon success">
            <div class="checkmark"></div>
        </div>
        
        <a href="index.php" class="back-home-btn">Back to Home</a>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>