<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinebox";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize the 'screening' param
if (isset($_GET['screening']) && is_numeric($_GET['screening'])) {
    $screening_id = (int) $_GET['screening'];
} else {
    die("Screening ID not provided");
}

// Get screening details, movie title, and cinema details
$screening_sql = "SELECT s.ScreeningID, s.ScreeningTime, s.TicketPrice, 
                         m.Title as movie_title, m.MovieID, m.poster,
                         c.CinemaID, c.Name as cinema_name
                  FROM screenings s
                  JOIN movies m ON s.MovieID = m.MovieID
                  JOIN cinemas c ON s.CinemaID = c.CinemaID
                  WHERE s.ScreeningID = ?";

$screening_stmt = $conn->prepare($screening_sql);
$screening_stmt->bind_param("i", $screening_id);
$screening_stmt->execute();
$screening_result = $screening_stmt->get_result();
$screening = $screening_result->fetch_assoc();

if (!$screening) {
    die("Screening not found");
}

// Get all seats for this cinema
$seats_sql = "SELECT s.SeatID, s.Row, s.Number,
                     CASE WHEN bs.BookingID IS NOT NULL 
                          AND b.ScreeningID = ? THEN 1 
                          ELSE 0 
                     END as is_booked
              FROM seats s
              LEFT JOIN bookedseats bs ON s.SeatID = bs.SeatID
              LEFT JOIN bookings b ON bs.BookingID = b.BookingID
              WHERE s.CinemaID = ?
              ORDER BY s.Row, s.Number";

$seats_stmt = $conn->prepare($seats_sql);
$seats_stmt->bind_param("ii", $screening_id, $screening['CinemaID']);
$seats_stmt->execute();
$seats_result = $seats_stmt->get_result();

// Organize seats by row
$seats = [];
while ($row = $seats_result->fetch_assoc()) {
    if (!isset($seats[$row['Row']])) {
        $seats[$row['Row']] = [];
    }
    $seats[$row['Row']][] = [
        'id' => $row['SeatID'],
        'number' => $row['Number'],
        'is_booked' => $row['is_booked']
    ];
}

// Format screening time
$screening_datetime = new DateTime($screening['ScreeningTime']);
$formatted_date = $screening_datetime->format('D, d-m-Y');
$formatted_time = $screening_datetime->format('H:i');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .seat-selection {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .screen {
            background: #444;
            height: 5px;
            width: 100%;
            margin: 2rem 0;
            position: relative;
        }
        .screen::before {
            content: "SCREEN";
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            color: #666;
            font-size: 0.8rem;
        }
        .seats-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin: 2rem 0;
        }
        .seat-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .row-label {
            width: 30px;
            text-align: center;
            color: #666;
        }
        .seat {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #444;
            transition: background-color 0.2s;
        }
        .seat[data-status="available"] {
            background: #444;
        }
        .seat[data-status="selected"] {
            background: #8BB8E8;
        }
        .seat[data-status="booked"] {
            background: #DC2626;
            cursor: not-allowed;
        }
        .seat:not([data-status="booked"]):hover {
            background: #666;
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        .summary {
            text-align: right;
            margin: 2rem 0;
        }
        .book-button {
            background: #DC2626;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }
        .book-button:disabled {
            background: #666;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    <div class="flex-container">
        <div class="poster-container">
            <?php if ($screening['poster']): ?>
                <img src="<?php echo htmlspecialchars($screening['poster']); ?>" alt="<?php echo htmlspecialchars($screening['movie_title']); ?> Poster">
            <?php endif; ?>
        </div>
        <div class="seat-selection">
            <div class="movie-header">
                <button class="back-button" onclick="window.history.back();">←</button>
                <h1 class="movie-title"><?php echo htmlspecialchars($screening['movie_title']); ?></h1>
            </div>
            
            <div class="details-section">
                <p><?php echo htmlspecialchars($screening['cinema_name']); ?></p>
                <p><?php echo $formatted_date . ' ' . $formatted_time; ?></p>
            </div>

            <div class="screen"></div>

            <div class="seats-container">
                <?php foreach ($seats as $row => $row_seats): ?>
                <div class="seat-row">
                    <div class="row-label"><?php echo htmlspecialchars($row); ?></div>
                    <?php foreach ($row_seats as $seat): ?>
                    <button class="seat" 
                            data-seat-id="<?php echo $seat['id']; ?>"
                            data-status="<?php echo $seat['is_booked'] ? 'booked' : 'available'; ?>"
                            <?php echo $seat['is_booked'] ? 'disabled' : ''; ?>>
                        <?php echo $seat['number']; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #444;"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #8BB8E8;"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #DC2626;"></div>
                    <span>Booked</span>
                </div>
            </div>

            <div class="summary">
                <p>Selected: <span id="selected-count">0</span> seats</p>
                <p>Total: $<span id="total-amount">0.00</span></p>
                <button id="book-button" class="book-button" disabled>Book Seats</button>
            </div>
        </div>
    </div>                    
    <script>
        const ticketPrice = <?php echo $screening['TicketPrice']; ?>;
        const screeningId = <?php echo $screening_id; ?>;
        let selectedSeats = [];

        document.querySelector('.seats-container').addEventListener('click', (e) => {
            const seat = e.target.closest('.seat');
            if (!seat || seat.dataset.status === 'booked') return;

            if (seat.dataset.status === 'available') {
                seat.dataset.status = 'selected';
                selectedSeats.push(seat.dataset.seatId);
            } else {
                seat.dataset.status = 'available';
                selectedSeats = selectedSeats.filter(id => id !== seat.dataset.seatId);
            }

            updateSummary();
        });

        function updateSummary() {
            const count = selectedSeats.length;
            const total = (count * ticketPrice).toFixed(2);
            
            document.getElementById('selected-count').textContent = count;
            document.getElementById('total-amount').textContent = total;
            document.getElementById('book-button').disabled = count === 0;
        }

        document.getElementById('book-button').addEventListener('click', () => {
            // Create a form to submit the booking
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process_booking.php';

            // Add screening ID
            const screeningInput = document.createElement('input');
            screeningInput.type = 'hidden';
            screeningInput.name = 'screening_id';
            screeningInput.value = screeningId;
            form.appendChild(screeningInput);

            // Add selected seats
            selectedSeats.forEach(seatId => {
                const seatInput = document.createElement('input');
                seatInput.type = 'hidden';
                seatInput.name = 'seats[]';
                seatInput.value = seatId;
                form.appendChild(seatInput);
            });

            document.body.appendChild(form);
            form.submit();
        });
    </script>
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