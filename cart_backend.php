<?php
require_once 'db_config.php';

function getBookings($userId = null) {
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if ($userId === null) {
        return [];
    }

    $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password_db'], $GLOBALS['dbname']);
    
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return [];
    }

    $sql = "SELECT 
                b.BookingID,
                b.TotalAmount,
                m.Title,
                m.Thumbnail,
                s.ScreeningTime,
                GROUP_CONCAT(CONCAT(st.Row, '-', st.Number) ORDER BY st.Row, st.Number) as SeatNumbers,
                COUNT(bs.SeatID) as SeatCount
            FROM Bookings b
            JOIN Screenings s ON b.ScreeningID = s.ScreeningID
            JOIN Movies m ON s.MovieID = m.MovieID
            JOIN BookedSeats bs ON b.BookingID = bs.BookingID
            JOIN Seats st ON bs.SeatID = st.SeatID
            WHERE b.UserID = ?
            GROUP BY b.BookingID";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'booking_id' => $row['BookingID'],
            'title' => $row['Title'],
            'thumbnail' => $row['Thumbnail'],
            'screening_date' => date('D, d-m-Y; g:ia', strtotime($row['ScreeningTime'])),
            'seats' => $row['SeatNumbers'],
            'seat_count' => $row['SeatCount'],
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