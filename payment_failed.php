<?php
require_once 'auth.inc.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-result {
            text-align: center;
            padding: 3rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .result-title {
            color: #DC2626;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .result-message {
            color: #fff;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .result-icon {
            width: 120px;
            height: 120px;
            background: #2A2A2A;
            border-radius: 50%;
            margin: 2rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cross {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .cross::before,
        .cross::after {
            content: '';
            position: absolute;
            background: #DC2626;
            width: 4px;
            height: 60px;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .cross::before {
            transform: translateX(-50%) rotate(45deg);
        }

        .cross::after {
            transform: translateX(-50%) rotate(-45deg);
        }

        .failed-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .retry-btn,
        .change-method-btn {
            background: #DC2626;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .retry-btn:hover,
        .change-method-btn:hover {
            background: #B91C1C;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content payment-result">
        <h1 class="result-title">Payment Failed</h1>
        <p class="result-message">Please retry or change your payment method</p>
        
        <div class="result-icon">
            <div class="cross"></div>
        </div>
        
        <div class="failed-buttons">
            <form action="payment_method.php" method="POST">
                <button type="submit" class="retry-btn">Retry Payment</button>
            </form>
            
            <form action="payment_method.php" method="POST">
                <button type="submit" class="change-method-btn">Change Payment Method</button>
            </form>
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
</body>
</html>