<?php
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
    
    <div class="main-content payment-page">
        <a href="cart.php" class="back-link">
            <span class="back-arrow">←</span> Payment Method
        </a>
        
        <div class="payment-content">
            <p class="payment-instruction">Please choose your preferred payment method</p>
            
            <div class="payment-methods">
                <div class="payment-method" data-method="paynow">
                    <div class="method-box">
                        <img src="images/placeholder.png" alt="PayNow">
                        <span>PayNow</span>
                    </div>
                </div>
                
                <div class="payment-method" data-method="credit-card">
                    <div class="method-box">
                        <img src="images/placeholder.png" alt="Debit/Credit Card">
                        <span>Debit/Credit Card</span>
                    </div>
                </div>
            </div>
            
            <button id="proceedBtn" class="proceed-button" disabled>Proceed to Pay</button>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="payment.js"></script>
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
</body>
</html>
