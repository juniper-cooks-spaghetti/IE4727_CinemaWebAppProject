<?php
require_once 'auth.inc.php';
require_once 'db_config.php';

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
$search_title = isset($_GET['title']) ? '%' . $conn->real_escape_string($_GET['title']) . '%' : '%';
$search_date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

$sql = "SELECT DISTINCT m.movieid, m.title, m.poster 
        FROM movies m
        JOIN screenings s ON m.movieid = s.movieid
        WHERE m.title LIKE ?
        AND (? = '' OR DATE(s.ScreeningTime) = ?)
        ORDER BY m.title ASC
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $search_title, $search_date, $search_date);
$stmt->execute();
$result = $stmt->get_result();
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
                /* Main Content and Catalogue Layout */
        .main-content {
            padding: 2rem;
        }

        .catalogue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .search-form input[type="text"], .search-form input[type="date"] {
            padding: 0.5rem;
            margin-right: 0.5rem;
        }

        .search-form button {
            padding: 0.5rem 1rem;
            background-color: #DC2626;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .catalogue {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: flex-start;
        }

        /* Movie Container */
        .movie-container {
            position: relative;
            background-size: cover;
            background-position: center;
            height: 350px;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            transition: transform 0.3s ease;
            flex: 0 0 calc(25% - 1rem); /* Show 4 items per row by default */
            min-width: 250px; /* Minimum width for smaller screens */
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .movie-container {
                flex: 0 0 calc(33.333% - 1rem); /* 3 items per row */
            }
        }

        @media (max-width: 900px) {
            .movie-container {
                flex: 0 0 calc(50% - 1rem); /* 2 items per row */
            }
        }

        @media (max-width: 600px) {
            .movie-container {
                flex: 0 0 100%; /* 1 item per row */
            }
        }

        .movie-container:hover {
            transform: scale(1.05);
        }

        /* Hover Overlay */
        .movie-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0);
            transition: background-color 0.3s ease;
        }

        .movie-container:hover::before {
            background-color: rgba(0, 0, 0, 0.2);
        }

        /* Title and Button */
        .movie-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .movie-container:hover .movie-content {
            opacity: 1;
        }

        .movie-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .movie-container a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #DC2626;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>

    <div class="main-content">
        <!-- Now Showing Header and Search Form -->
        <div class="catalogue-header">
            <h2>Now Showing</h2>
            <form class="search-form" method="GET" action="">
                <input type="text" name="title" placeholder="Search by Title" value="<?php echo htmlspecialchars($_GET['title'] ?? ''); ?>">
                <input type="date" name="date" value="<?php echo htmlspecialchars($_GET['date'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Catalogue Grid -->
        <div class="catalogue">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($movie = $result->fetch_assoc()): ?>
                    <div class="movie-container" style="background-image: url('<?php echo htmlspecialchars($movie['poster']); ?>');">
                        <div class="movie-content">
                            <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                            <a href="filminfo.php?movieid=<?php echo $movie['movieid']; ?>">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No movies found matching your search criteria.</p>
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
$stmt->close();
$conn->close();
?>

