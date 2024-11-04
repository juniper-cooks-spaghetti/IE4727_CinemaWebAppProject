<?php
require_once 'auth.inc.php';
checkLogin();
require_once 'cart_backend.php';
$bookings = getBookings($_SESSION['user_id']);
$totalAmount = calculateTotal($bookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CineBox</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="main-content booking-page">
        <h1 class="page-title">My Bookings</h1>

        <div class="bookings-container">
            <div class="booking-list">
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $cart): ?>
                        <?php foreach ($cart['items'] as $item): ?>
                            <div class="booking-item">
                                <div class="movie-poster">
                                    <img src="<?php echo htmlspecialchars($item['poster']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?> Poster">
                                </div>
                                <div class="booking-details">
                                    <h2 class="movie-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                                    <p class="screening-time"><?php echo $item['screening_time']; ?></p>
                                    <p class="seat-info"><?php echo substr_count($item['seats'], ',') + 1; ?> pax, <?php echo htmlspecialchars($item['seats']); ?></p>
                                </div>
                                <button class="edit-button">Edit Details</button>
                                <div class="booking-price">
                                    <?php 
                                    $seat_count = substr_count($item['seats'], ',') + 1;
                                    echo $seat_count . "pax x $10 = $" . number_format($seat_count * 10, 2); 
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <div class="total-amount">Total: $<?php echo number_format($totalAmount, 2); ?></div>
                <?php else: ?>
                    <p class="no-bookings">No bookings found</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($bookings)): ?>
                <div class="cart-footer">
                    <button class="checkout-button">Check Out</button>
                </div>
            <?php endif; ?>
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
    <script src="cart.js"></script>
</body>
</html>