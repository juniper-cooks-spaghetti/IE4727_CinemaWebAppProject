<?php
require_once 'db_config.php';

function getBookings($userId = null) {
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if ($userId === null) {
        return [];
    }

    $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username_db'], $GLOBALS['password_db'], $GLOBALS['dbname']);
    
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return [];
    }

    // Modified query to group by CartID and include movie poster
    $sql = "SELECT 
                b.CartID,
                GROUP_CONCAT(b.BookingID) as BookingIDs,
                SUM(b.TotalAmount) as CartTotal,
                GROUP_CONCAT(m.Title) as Titles,
                GROUP_CONCAT(m.Poster) as Posters,
                GROUP_CONCAT(s.ScreeningTime) as ScreeningTimes,
                GROUP_CONCAT(
                    (
                        SELECT GROUP_CONCAT(CONCAT(st.Row, '-', st.Number))
                        FROM BookedSeats bs2
                        JOIN Seats st ON bs2.SeatID = st.SeatID
                        WHERE bs2.BookingID = b.BookingID
                        ORDER BY st.Row, st.Number
                    )
                ) as SeatNumbers,
                SUM(
                    (
                        SELECT COUNT(*)
                        FROM BookedSeats bs3
                        WHERE bs3.BookingID = b.BookingID
                    )
                ) as TotalSeats
            FROM Bookings b
            JOIN Screenings s ON b.ScreeningID = s.ScreeningID
            JOIN Movies m ON s.MovieID = m.MovieID
            WHERE b.UserID = ?
            GROUP BY b.CartID
            ORDER BY MIN(b.BookingTime) DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        // Split concatenated strings into arrays
        $titles = explode(',', $row['Titles']);
        $posters = explode(',', $row['Posters']);
        $screeningTimes = explode(',', $row['ScreeningTimes']);
        $seatNumbers = explode(',', $row['SeatNumbers']);
        
        // Format cart summary
        $cartSummary = [];
        for ($i = 0; $i < count($titles); $i++) {
            $cartSummary[] = [
                'title' => $titles[$i],
                'poster' => $posters[$i],
                'screening_time' => date('D, d-m-Y; g:ia', strtotime($screeningTimes[$i])),
                'seats' => $seatNumbers[$i]
            ];
        }

        $bookings[] = [
            'cart_id' => $row['CartID'],
            'booking_ids' => explode(',', $row['BookingIDs']),
            'items' => $cartSummary,
            'total_seats' => $row['TotalSeats'],
            'total_amount' => $row['CartTotal']
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