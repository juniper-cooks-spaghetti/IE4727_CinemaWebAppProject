<?php
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinebox";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function getBookings() {
    $conn = getDatabaseConnection();
    // For demo purposes, using UserID = 1. In production, get from session
    $userId = 1;

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

// API endpoint for updating booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'edit_booking':
            $response = handleEditBooking($_POST['booking_id']);
            echo json_encode($response);
            break;
            
        case 'delete_booking':
            $response = handleDeleteBooking($_POST['booking_id']);
            echo json_encode($response);
            break;
    }
    exit;
}

function handleEditBooking($bookingId) {
    // Implement editing logic here
    return [
        'success' => true,
        'message' => 'Booking updated successfully'
    ];
}

function handleDeleteBooking($bookingId) {
    // Implement deletion logic here
    return [
        'success' => true,
        'message' => 'Booking deleted successfully'
    ];
}