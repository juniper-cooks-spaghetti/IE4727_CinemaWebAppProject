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
        // Get current pending bookings details and user email
        $query = "SELECT b.BookingID, m.Title, s.ScreeningTime, b.TotalAmount, 
                        GROUP_CONCAT(CONCAT(st.Row, '-', st.Number)) as seats,
                        u.Email as UserEmail
                 FROM Bookings b
                 JOIN Screenings s ON b.ScreeningID = s.ScreeningID
                 JOIN Movies m ON s.MovieID = m.MovieID
                 JOIN BookedSeats bs ON b.BookingID = bs.BookingID
                 JOIN Seats st ON bs.SeatID = st.SeatID
                 JOIN Users u ON b.UserID = u.UserID
                 WHERE b.UserID = ? AND b.payment_status = 'pending'
                 GROUP BY b.BookingID";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("No pending bookings found");
        }

        // Prepare email content
        $message = "Your payment with CineBox has been confirmed, here are your booking details:\n\n";
        $userEmail = null;
        $bookingIds = [];

        while ($booking = $result->fetch_assoc()) {
            // Store the booking ID for later update
            $bookingIds[] = $booking['BookingID'];
            
            // Store the user's email (will be the same for all bookings)
            if (!$userEmail) {
                $userEmail = $booking['UserEmail'];
            }
            
            $message .= "Movie: " . $booking['Title'] . "\n";
            $message .= "Time: " . date('D, d-m-Y H:i', strtotime($booking['ScreeningTime'])) . "\n";
            $message .= "Seats: " . $booking['seats'] . "\n";
            $message .= "Amount: $" . number_format($booking['TotalAmount'], 2) . "\n\n";
        }

        // Add footer
        $message .= "Please show this e-mail in the theatre to print your ticket.\n";
        $message .= "Thank you for watching at CineBox 🎬🍿!\n";
        $message .= "------------------------\n";
        $message .= "CineBox Singapore\n";
        $message .= "26 Street, 380381 Singapore\n";
        $message .= "feedback@cinebox.com";

        // Send email
        if ($userEmail) {
            $to = $userEmail;
            $subject = "CineBox Booking Confirmation";
            $headers = "From: CineBox <cinebox@localhost>" . "\r\n" .
                      "Reply-To: cinebox@localhost" . "\r\n" .
                      "Content-Type: text/plain; charset=UTF-8" . "\r\n" .
                      "X-Mailer: PHP/" . phpversion();

            if (!mail($to, $subject, $message, $headers)) {
                throw new Exception("Failed to send confirmation email");
            }

            // Update payment status for all pending bookings
            $updateQuery = "UPDATE Bookings SET payment_status = 'success' 
                          WHERE UserID = ? AND payment_status = 'pending'";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $_SESSION['user_id']);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update booking status");
            }
            $updateStmt->close();
        }

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
    error_log("Payment processing error: " . $e->getMessage());
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