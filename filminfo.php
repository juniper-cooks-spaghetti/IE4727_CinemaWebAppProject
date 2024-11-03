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

if (isset($_GET['movieid']) && is_numeric($_GET['movieid']))    {
    $movieid = (int) $_GET['movieid']; //typecasting safety measure
}

// Query to get the first 4 movies
$sql = "SELECT title, genre, duration, synopsis, releasedate, poster FROM movies WHERE movieid = ?";
$stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movieid); // Bind 'movieid' as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($movie = $result->fetch_assoc()) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cinebox</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">CineBox</div>
        <nav>
            <a href="">Home</a>
            <a href="#catalogue">Catalogue</a>
            <a href="#cart">My Bookings</a>
        </nav>
        <div class="profile-icon">👤</div>
    </header>
    <div class="flex-container">
        <div class="poster-container">
            <img src="<?php echo htmlspecialchars($movie['poster']) . '?auto=compress&cs=tinysrgb&dpr=1&w=1500'; ?>" alt="Image Description">
        </div>
        <div class="text-container">
            <div class="movie-header">
            <button class="back-button" onclick="window.history.back();">←</button>
            <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
            <button class="buy-tickets">Buy Tickets</button>
            </div>
            <hr>
            <div class="details-section">
            <h2 class="section-title">Details</h2>
            <div class="details-container">
                <div class="detail-row">
                <div class="label">Genre</div>
                <div class="value"><?php echo htmlspecialchars($movie['genre']); ?></div>
                </div>
                
                <div class="detail-row">
                <div class="label">Release</div>
                <div class="value"><?php echo htmlspecialchars($movie['releasedate']); ?></div>
                </div>
                
                <div class="detail-row">
                <div class="label">Runtime</div>
                <div class="value"><?php echo htmlspecialchars($movie['duration']); ?> minutes</div>
                </div>
                
            </div>
            </div>
            <hr>
            <div class="synopsis-section">
            <h2 class="section-title">Synopsis</h2>
            <p class="synopsis"><?php echo htmlspecialchars($movie['synopsis']); ?></p>
            </div>
        </div>
    </div>
    <?php
    } else {
        // Movie not found
        echo "<p>Movie not found.</p>";
    }

    $stmt->close();
    ?>
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
$conn->close();
?>