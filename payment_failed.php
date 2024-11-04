<?php
// payment_failed.php
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
        <h2 class="result-title">Payment Failed</h2>
        <p class="result-message">Please retry or change your payment method</p>
        
        <div class="result-icon failed">
            <div class="cross"></div>
        </div>
        
        <div class="failed-buttons">
            <button class="retry-btn" onclick="retryPayment()">Retry Payment</button>
            <button class="change-method-btn" onclick="changePaymentMethod()">Change Payment Method</button>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="payment_failed.js"></script>
</body>
</html>