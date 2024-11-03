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

// Query to get the first 4 movies
$sql = "SELECT movieid, title, thumbnail FROM movies LIMIT 4";
$result = $conn->query($sql);
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
            <a href="cart.php">My Bookings</a>
        </nav>
        <div class="profile-icon">👤</div>
    </header>
    
    <div class="carousel" duration="5000">
        <ul tabindex="0">
          <?php
          $slideIndex = 1; // Counter for unique slide IDs
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '<li id="slide' . $slideIndex . '" style="background-image: url(' . htmlspecialchars($row['thumbnail']) . ');">';
                  echo '<div>' . htmlspecialchars($row['title']) . '<br />';
                //   echo '<a href="#">Book Now</a></div>';
                  echo '<form action="filminfo.php" method="get" style="display:inline;">';
                  echo '<input type="hidden" name="movieid" value="' . htmlspecialchars($row['movieid']) . '">';
                  echo '<button type="submit">View Movie</button>';
                  echo '</form></div>';
                  echo '</li>';
                  $slideIndex++;
              }
          } else {
              echo "<li>No movies available</li>";
          }
          ?>
        </ul>
        <ol>
          <?php
          for ($i = 1; $i <= $slideIndex - 1; $i++) {
              echo '<li><a href="#slide' . $i . '"></a></li>';
          }
          ?>
        </ol>
        <div class="prev">&lsaquo;</div>
        <div class="next">&rsaquo;</div>
    </div>
    
    <div class="main-content">
        <h2>About Cinebox</h2>
        <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    </div>
    
    <script src="index.js"></script>
    
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

