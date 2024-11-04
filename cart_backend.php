<?php
require_once 'db_config.php';

// Check if database configuration variables are set
if (!isset($servername, $username_db, $password_db, $dbname)) {
    die("Database configuration variables are not set.");
}

function getBookings($userId = null) {
    // Check if userId is provided or fetch from session
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if ($userId === null) {
        return [];
    }

    // Create a new mysqli connection
    global $servername, $username_db, $password_db, $dbname; // Use global variables
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    
    // Check for connection errors
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        echo "Connection failed: " . htmlspecialchars($conn->connect_error); // Output error for debugging
        return [];
    }

    // SQL query to fetch bookings
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
            GROUP BY b.BookingID";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("SQL prepare failed: " . htmlspecialchars($conn->error));
        echo "SQL prepare failed: " . htmlspecialchars($conn->error); // Output error for debugging
        $conn->close();
        return [];
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch bookings into an array
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        // Debugging: Log the fetched row to see all fields
        error_log(print_r($row, true)); // Log the row to see what data is being retrieved

        $bookings[] = [
            'booking_id' => $row['BookingID'],
            'title' => $row['Title'],
            'thumbnail' => $row['Thumbnail'],
            'screening_date' => date('D, d-m-Y; g:ia', strtotime($row['ScreeningTime'])),
            'seats' => $row['SeatNumbers'],
            'seat_count' => $row['SeatCount'],
            'poster' => $row['Poster'], // Check if this is populated correctly
            'total_amount' => $row['TotalAmount']
        ];
    }

    // Check if any bookings are retrieved
    if (empty($bookings)) {
        error_log("No bookings found for user ID: " . $userId);
    }

    // Clean up
    $stmt->close();
    $conn->close();

    return $bookings;
}

function calculateTotal($bookings) {
    return array_reduce($bookings, function($carry, $booking) {
        return $carry + $booking['total_amount'];
    }, 0);
}
