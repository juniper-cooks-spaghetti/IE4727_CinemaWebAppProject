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

// Get payment method from POST
$paymentMethod = $_POST['payment_method'] ?? '';

if (empty($paymentMethod)) {
    header("Location: payment_method.php");
    exit();
}

try {
    $conn->begin_transaction();

    if ($paymentMethod === 'credit-card') {
        // Update payment status to success
        $stmt = $conn->prepare("UPDATE Bookings SET payment_status = 'success' 
                              WHERE UserID = ? AND payment_status = 'pending'");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();

        // Get booking details for email
        $query = "SELECT b.BookingID, m.Title, s.ScreeningTime, b.TotalAmount, 
                        GROUP_CONCAT(CONCAT(st.Row, '-', st.Number)) as seats
                 FROM Bookings b
                 JOIN Screenings s ON b.ScreeningID = s.ScreeningID
                 JOIN Movies m ON s.MovieID = m.MovieID
                 JOIN BookedSeats bs ON b.BookingID = bs.BookingID
                 JOIN Seats st ON bs.SeatID = st.SeatID
                 WHERE b.UserID = ? AND b.payment_status = 'success'
                 GROUP BY b.BookingID";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Prepare email content
        $message = "Your booking confirmation:\n\n";
        while ($booking = $result->fetch_assoc()) {
            $message .= "Movie: " . $booking['Title'] . "\n";
            $message .= "Time: " . date('D, d-m-Y H:i', strtotime($booking['ScreeningTime'])) . "\n";
            $message .= "Seats: " . $booking['seats'] . "\n";
            $message .= "Amount: $" . number_format($booking['TotalAmount'], 2) . "\n\n";
        }
        
        // Simple email sending - works in XAMPP
        $to = "f32ee@localhost";
        $subject = "CineBox Booking Confirmation";
        $headers = "From: f32ee@localhost";

        // Send email
        mail($to, $subject, $message, $headers);

        $conn->commit();
        header("Location: payment_success.php");
        exit();
    } 
    else if ($paymentMethod === 'paynow') {
        $conn->rollback();
        header("Location: payment_failed.php");
        exit();
    }

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    header("Location: payment_failed.php");
    exit();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>