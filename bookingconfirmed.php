<?php
require_once 'auth.inc.php';
checkLogin();
require_once 'db_config.php';

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Create booking record
        $booking_sql = "INSERT INTO bookings (UserID, ScreeningID, BookingTime, TotalAmount) 
                       VALUES (?, ?, NOW(), ?)";
        $booking_stmt = $conn->prepare($booking_sql);
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $booking_stmt->bind_param("iid", $user_id, $_POST['screening_id'], $_POST['total_amount']);
        $booking_stmt->execute();
        $booking_id = $booking_stmt->insert_id;

        // Insert booked seats
        $seats_sql = "INSERT INTO bookedseats (BookingID, SeatID) VALUES (?, ?)";
        $seats_stmt = $conn->prepare($seats_sql);
        foreach ($_POST['seats'] as $seat_id) {
            $seats_stmt->bind_param("ii", $booking_id, $seat_id);
            $seats_stmt->execute();
        }

        // Get booking details for display
        $details_sql = "SELECT s.ScreeningTime, m.Title, m.Poster,
                              GROUP_CONCAT(CONCAT(st.Row, '-', st.Number) ORDER BY st.Row, st.Number) as seats
                       FROM bookings b
                       JOIN screenings s ON b.ScreeningID = s.ScreeningID
                       JOIN movies m ON s.MovieID = m.MovieID
                       JOIN bookedseats bs ON b.BookingID = bs.BookingID
                       JOIN seats st ON bs.SeatID = st.SeatID
                       WHERE b.BookingID = ?
                       GROUP BY b.BookingID";
        $details_stmt = $conn->prepare($details_sql);
        $details_stmt->bind_param("i", $booking_id);
        $details_stmt->execute();
        $booking = $details_stmt->get_result()->fetch_assoc();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Booking failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 2rem auto;
            text-align: center;
            padding: 0 1rem;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            margin: 2rem auto;
        }
        .success-icon svg {
            width: 100%;
            height: 100%;
        }
        .booking-details {
            margin: 2rem 0;
            text-align: left;
        }
        .booking-details h2 {
            margin-bottom: 1rem;
        }
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .action-button {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .primary-button {
            background: #DC2626;
            color: white;
            border: none;
        }
        .secondary-button {
            background: #374151;
            color: white;
            border: none;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="confirmation-container">
        <div class="success-icon">
            <svg viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#4CAF50" stroke-width="5"/>
                <path d="M25 50 L45 70 L75 30" fill="none" stroke="#4CAF50" stroke-width="5"/>
            </svg>
        </div>

        <h1>Successfully added to your booking</h1>

        <div class="booking-details">
            <h2><?php echo htmlspecialchars($booking['Title']); ?></h2>
            <p>Date & Time: <?php echo date('D, d-m-Y H:i', strtotime($booking['ScreeningTime'])); ?></p>
            <p>Seats: <?php echo htmlspecialchars($booking['seats']); ?></p>
            <p>Total Price: $<?php echo number_format($_POST['total_amount'], 2); ?></p>
        </div>

        <div class="button-group">
            <a href="index.php" class="action-button secondary-button">Back to Catalogue</a>
            <a href="cart.php" class="action-button primary-button">View My Bookings</a>
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

<?php
if (isset($booking_stmt)) $booking_stmt->close();
if (isset($seats_stmt)) $seats_stmt->close();
if (isset($details_stmt)) $details_stmt->close();
$conn->close();
?>