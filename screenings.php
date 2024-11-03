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

// Retrieve and sanitize the 'movieid' param
if (isset($_GET['movieid']) && is_numeric($_GET['movieid'])) {
    $movieid = (int) $_GET['movieid'];
} else {
    die("Movie ID not provided");
}

// First, get the movie details
$movie_sql = "SELECT title, poster FROM movies WHERE MovieID = ?";
$movie_stmt = $conn->prepare($movie_sql);
$movie_stmt->bind_param("i", $movieid);
$movie_stmt->execute();
$movie_result = $movie_stmt->get_result();
$movie = $movie_result->fetch_assoc();

if (!$movie) {
    die("Movie not found");
}

// Then, get the screenings
$screening_sql = "SELECT c.CinemaID, c.Name as venue_name, 
                 s.ScreeningTime, s.TicketPrice, s.ScreeningID
                 FROM screenings s
                 JOIN cinemas c ON s.CinemaID = c.CinemaID
                 WHERE s.MovieID = ? AND s.ScreeningTime > NOW()
                 ORDER BY c.CinemaID, s.ScreeningTime";

$screening_stmt = $conn->prepare($screening_sql);
$screening_stmt->bind_param("i", $movieid);
$screening_stmt->execute();
$screening_result = $screening_stmt->get_result();

// Group screenings by venue
$venues = [];

while ($row = $screening_result->fetch_assoc()) {
    $venue_id = $row['CinemaID'];
    if (!isset($venues[$venue_id])) {
        $venues[$venue_id] = [
            'name' => $row['venue_name'],
            'screenings' => []
        ];
    }
    
    // Format date and time
    $screening_datetime = new DateTime($row['ScreeningTime']);
    $date = $screening_datetime->format('D, d F Y');
    $time = $screening_datetime->format('g:ia');
    
    // Group screenings by date
    if (!isset($venues[$venue_id]['screenings'][$date])) {
        $venues[$venue_id]['screenings'][$date] = [
            'price' => $row['TicketPrice'],
            'times' => []
        ];
    }
    
    $venues[$venue_id]['screenings'][$date]['times'][] = [
        'time' => $time,
        'screening_id' => $row['ScreeningID']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CineBox</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .venue-container {
            background: #2A2A2A;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .venue-name {
            color: #8BB8E8;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .date-section {
            margin-bottom: 1rem;
        }
        .date-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .price-tag {
            color: #8BB8E8;
        }
        .time-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .time-button {
            background: #DC2626;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .time-button:hover {
            background: #B91C1C;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>
    <div class="flex-container">
        <div class="poster-container">
            <?php if ($movie['poster']): ?>
                <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Poster">
            <?php endif; ?>
        </div>
        <div class="text-container">
            <div class="movie-header">
                <button class="back-button" onclick="window.history.back();">←</button>
                <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
            </div>
            <h2>Please select a venue and time.</h2>
            <hr>
            
            <?php if (empty($venues)): ?>
                <p>No screenings available for this movie.</p>
            <?php else: ?>
                <?php foreach ($venues as $venue): ?>
                <div class="venue-container">
                    <h3 class="venue-name"><?php echo htmlspecialchars($venue['name']); ?></h3>
                    <?php foreach ($venue['screenings'] as $date => $screening): ?>
                    <div class="date-section">
                        <div class="date-header">
                            <span><?php echo htmlspecialchars($date); ?></span>
                            <span class="price-tag">$<?php echo number_format($screening['price'], 2); ?></span>
                        </div>
                        <div class="time-buttons">
                            <?php foreach ($screening['times'] as $time_slot): ?>
                            <a href="seats.php?screening=<?php echo $time_slot['screening_id']; ?>" 
                               class="time-button">
                                <?php echo htmlspecialchars($time_slot['time']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
$movie_stmt->close();
$screening_stmt->close();
$conn->close();
?>