<?php
require_once 'db_config.php';

function getBookings($userId = null) {
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if ($userId === null) {
        return [];
    }

    global $servername, $username_db, $password_db, $dbname;
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return [];
    }

    // Modified query to ONLY get pending bookings
    $sql = "SELECT 
                b.BookingID,
                b.TotalAmount,
                m.Title,
                m.Poster,
                s.ScreeningTime,
                GROUP_CONCAT(CONCAT(st.Row, '-', st.Number) ORDER BY st.Row, st.Number) AS SeatNumbers,
                COUNT(bs.SeatID) AS SeatCount
            FROM Bookings b
            JOIN Screenings s ON b.ScreeningID = s.ScreeningID
            JOIN Movies m ON s.MovieID = m.MovieID
            JOIN BookedSeats bs ON b.BookingID = bs.BookingID
            JOIN Seats st ON bs.SeatID = st.SeatID
            WHERE b.UserID = ? 
            AND b.payment_status = 'pending'  /* Only get pending bookings */
            GROUP BY b.BookingID
            ORDER BY s.ScreeningTime ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'booking_id' => $row['BookingID'],
            'title' => $row['Title'],
            'screening_date' => date('D, d-m-Y; g:ia', strtotime($row['ScreeningTime'])),
            'seats' => $row['SeatNumbers'],
            'seat_count' => $row['SeatCount'],
            'poster' => $row['Poster'],
            'total_amount' => $row['TotalAmount']
        ];
    }

    $stmt->close();
    $conn->close();

    return $bookings;
}

function calculateTotal($bookings) {
    return array_reduce($bookings, function($carry, $booking) {
        return $carry + $booking['total_amount'];
    }, 0);
}

function deleteBooking($bookingId, $userId) {
    $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username_db'], $GLOBALS['password_db'], $GLOBALS['dbname']);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Verify the booking belongs to the user
        $stmt = $conn->prepare("SELECT BookingID FROM Bookings WHERE BookingID = ? AND UserID = ?");
        $stmt->bind_param("ii", $bookingId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Booking not found or unauthorized");
        }

        // Delete booked seats first (due to foreign key constraint)
        $stmt = $conn->prepare("DELETE FROM BookedSeats WHERE BookingID = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        // Then delete the booking
        $stmt = $conn->prepare("DELETE FROM Bookings WHERE BookingID = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw $e;
    } finally {
        $stmt->close();
        $conn->close();
    }
}

function editBookingSeats($bookingId, $userId, $newSeats) {
    $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username_db'], $GLOBALS['password_db'], $GLOBALS['dbname']);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Verify the booking belongs to the user and get screening details
        $stmt = $conn->prepare("SELECT b.BookingID, s.TicketPrice, s.ScreeningTime 
                              FROM Bookings b 
                              JOIN Screenings s ON b.ScreeningID = s.ScreeningID 
                              WHERE b.BookingID = ? AND b.UserID = ?");
        $stmt->bind_param("ii", $bookingId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();

        if (!$booking) {
            throw new Exception("Booking not found or unauthorized");
        }

        // Check if screening hasn't started yet
        if (strtotime($booking['ScreeningTime']) < time()) {
            throw new Exception("Cannot modify booking for past screenings");
        }

        // Delete existing booked seats
        $stmt = $conn->prepare("DELETE FROM BookedSeats WHERE BookingID = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        // Insert new seats
        $stmt = $conn->prepare("INSERT INTO BookedSeats (BookingID, SeatID) VALUES (?, ?)");
        foreach ($newSeats as $seatId) {
            $stmt->bind_param("ii", $bookingId, $seatId);
            $stmt->execute();
        }

        // Update total amount
        $newTotalAmount = count($newSeats) * $booking['TicketPrice'];
        $stmt = $conn->prepare("UPDATE Bookings SET TotalAmount = ? WHERE BookingID = ?");
        $stmt->bind_param("di", $newTotalAmount, $bookingId);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw $e;
    } finally {
        $stmt->close();
        $conn->close();
    }
}
?>