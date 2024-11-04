<?php
require_once 'auth.inc.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-result {
            text-align: center;
            padding: 3rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .result-title {
            color: #8BB8E8;
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
            position: relative;
        }

        .checkmark::after {
            content: '';
            display: block;
            width: 40px;
            height: 60px;
            border-right: 4px solid #4CAF50;
            border-bottom: 4px solid #4CAF50;
            transform: rotate(45deg);
            margin-top: -10px;
        }

        .back-home-btn {
            display: inline-block;
            background: #8BB8E8;
            color: white;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            margin-top: 2rem;
            transition: background-color 0.3s;
        }

        .back-home-btn:hover {
            background: #7AA7C7;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content payment-result">
        <h1 class="result-title">Payment Successful</h1>
        <p class="result-message">Please check your e-mail for your ticket(s)</p>
        
        <div class="result-icon">
            <div class="checkmark"></div>
        </div>
        
        <a href="index.php" class="back-home-btn">Back to Home</a>
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