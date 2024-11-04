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
    <style>
        .method-box {
            width: 200px;
            height: 150px;
            background: #2A2A2A;
            border: 2px solid transparent;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: border-color 0.3s;
            cursor: pointer;
        }

        .method-box.selected {
            border-color: #8BB8E8;
        }

        .method-box img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .method-box span {
            color: #fff;
        }

        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .proceed-button {
            background: #DC2626;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity 0.3s;
            width: 200px;
            margin: 0 auto;
            display: block;
        }

        .proceed-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .proceed-button:not(:disabled):hover {
            background: #B91C1C;
        }

        .payment-instruction {
            text-align: center;
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content payment-page">
        <a href="cart.php" class="back-link">
            <span class="back-arrow">←</span> Payment Method
        </a>
        
        <div class="payment-content">
            <p class="payment-instruction">Please choose your preferred payment method</p>
            
            <form action="process_payment.php" method="POST">
                <div class="payment-methods">
                    <div class="payment-method" onclick="selectPaymentMethod('credit-card')">
                        <div class="method-box" id="credit-card-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#8BB8E8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <line x1="2" y1="10" x2="22" y2="10"/>
                            </svg>
                            <span>Credit/Debit Card</span>
                        </div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPaymentMethod('paynow')">
                        <div class="method-box" id="paynow-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#8BB8E8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M8 12h8"/>
                                <path d="M12 8v8"/>
                            </svg>
                            <span>PayNow</span>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="payment_method" id="payment_method">
                <button type="submit" class="proceed-button" id="proceed-button" disabled>Proceed to Pay</button>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        function selectPaymentMethod(method) {
            // Remove selected class from all boxes
            document.querySelectorAll('.method-box').forEach(box => {
                box.classList.remove('selected');
            });
            
            // Add selected class to chosen method
            document.getElementById(method + '-box').classList.add('selected');
            
            // Update hidden input and enable button
            document.getElementById('payment_method').value = method;
            document.getElementById('proceed-button').disabled = false;
        }
    </script>
</body>
</html>