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
?>