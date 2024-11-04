<?php
require_once 'auth.inc.php';
require_once 'db_config.php';


// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify if we have the required POST data
if (!isset($_POST['screening_id']) || !isset($_POST['seats']) || !isset($_POST['total_amount'])) {
    die("Missing required booking information");
}

$screening_id = (int)$_POST['screening_id'];
$seats = array_map('intval', $_POST['seats']);
$total_amount = (float)$_POST['total_amount'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Get screening and movie details
$screening_sql = "SELECT s.ScreeningTime, m.Title, m.Poster 
                 FROM screenings s
                 JOIN movies m ON s.MovieID = m.MovieID
                 WHERE s.ScreeningID = ?";

$screening_stmt = $conn->prepare($screening_sql);
$screening_stmt->bind_param("i", $screening_id);
$screening_stmt->execute();
$screening_result = $screening_stmt->get_result();
$screening = $screening_result->fetch_assoc();

// Get seat information
$seats_sql = "SELECT Row, Number FROM seats WHERE SeatID IN (" . str_repeat('?,', count($seats) - 1) . '?)';
$seats_stmt = $conn->prepare($seats_sql);
$seats_stmt->bind_param(str_repeat('i', count($seats)), ...$seats);
$seats_stmt->execute();
$seats_result = $seats_stmt->get_result();

$seat_info = [];
while ($row = $seats_result->fetch_assoc()) {
    $seat_info[] = $row['Row'] . '-' . $row['Number'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .flex-container {
          position: relative;
        }
        .booking-details {
            flex: 1;
            padding-left: 3rem;
            padding-top: 3rem;
        }
        .details-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }
        .details-label {
            color: #666;
        }
        .confirm-button {
            background: #DC2626;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            float: right;
            position: absolute;
            bottom: 10px;
            right: 10px; 
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    
    <div class="flex-container">
        <div class="poster-container">
            <?php if ($screening['Poster']): ?>
                <img src="<?php echo htmlspecialchars($screening['Poster']); ?>" 
                     alt="<?php echo htmlspecialchars($screening['Title']); ?> Poster">
            <?php endif; ?>
        </div>
        
        <div class="booking-details">
            <h1><?php echo htmlspecialchars($screening['Title']); ?></h1>
            
            <div class="details-grid">
                <div class="details-label">Date & Time:</div>
                <div><?php echo date('D, d-m-Y H:i', strtotime($screening['ScreeningTime'])); ?></div>
                
                <div class="details-label">Seats:</div>
                <div><?php echo htmlspecialchars(implode(', ', $seat_info)); ?></div>
                
                <div class="details-label">Total Amount:</div>
                <div>$<?php echo number_format($total_amount, 2); ?></div>
            </div>

            <form action="process_booking.php" method="POST">
                <input type="hidden" name="screening_id" value="<?php echo $screening_id; ?>">
                <?php foreach($seats as $seat_id): ?>
                    <input type="hidden" name="seats[]" value="<?php echo $seat_id; ?>">
                <?php endforeach; ?>
                <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                
                <button type="submit" class="confirm-button">Confirm Booking</button>
            </form>
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
$screening_stmt->close();
$seats_stmt->close();
$conn->close();
?>