<?php
include 'dbc.php'; // Include your database connection

// Initialize variables
$selected_location = '';
$selected_hall = '';
$halls = [];
$movies = [];

// Fetch all unique locations
$query_locations = "SELECT DISTINCT location FROM mlocation";
$locations_result = $con->query($query_locations);

// Handle location selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['location'])) {
        $selected_location = $_POST['location'];

        // Fetch halls for the selected location
        $query_halls = "SELECT id, hall_name FROM mlocation WHERE location = ?";
        $stmt_halls = $con->prepare($query_halls);
        $stmt_halls->bind_param("s", $selected_location);
        $stmt_halls->execute();
        $halls_result = $stmt_halls->get_result();
        $halls = $halls_result->fetch_all(MYSQLI_ASSOC);
    }

    if (isset($_POST['hall_id'])) {
        $selected_hall = $_POST['hall_id'];

        // Fetch movies for the selected hall, including available seats
        $query_movies = "
            SELECT 
                m.id AS movie_id, 
                m.name AS movie_name, 
                m.trailer_url, 
                m.details AS movie_details, 
                mh.seats AS available_seats 
            FROM 
                movie m
            INNER JOIN 
                movie_hall mh ON m.hall_id = mh.id
            WHERE 
                mh.id = ?";
        $stmt_movies = $con->prepare($query_movies);
        $stmt_movies->bind_param("i", $selected_hall);
        $stmt_movies->execute();
        $movies_result = $stmt_movies->get_result();
        $movies = $movies_result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        form {
            margin: 20px 0;
        }

        select, input, button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            max-width: 300px;
        }

        .movie-item {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Book Your Movie Tickets</h1>

    <!-- Location Selection -->
    <form method="POST">
        <label for="location">Select Location:</label>
        <select name="location" id="location" onchange="this.form.submit()" required>
            <option value="">--Select Location--</option>
            <?php while ($location = $locations_result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($location['location']) ?>" <?= $selected_location === $location['location'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($location['location']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- Hall Selection -->
    <?php if (!empty($halls)): ?>
        <form method="POST">
            <input type="hidden" name="location" value="<?= htmlspecialchars($selected_location) ?>">
            <label for="hall">Select Hall:</label>
            <select name="hall_id" id="hall" onchange="this.form.submit()" required>
                <option value="">--Select Hall--</option>
                <?php foreach ($halls as $hall): ?>
                    <option value="<?= $hall['id'] ?>" <?= $selected_hall == $hall['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($hall['hall_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>

    <!-- Movie Selection -->
    <?php if (!empty($movies)): ?>
        <h2>Available Movies</h2>
        <?php foreach ($movies as $movie): ?>
            <div class="movie-item">
                <h3><?= htmlspecialchars($movie['movie_name']) ?></h3>
                <p><?= htmlspecialchars($movie['movie_details']) ?></p>
                <p><strong>Available Seats: <?= $movie['available_seats'] ?></strong></p>
                <p><a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank">Watch Trailer</a></p>
                <form method="POST" action="book.php">
                    <input type="hidden" name="movie_id" value="<?= $movie['movie_id'] ?>">
                    <label for="user_name">Your Name:</label>
                    <input type="text" name="user_name" required>
                    <label for="seats_to_book">Number of Seats:</label>
                    <input type="number" name="seats_to_book" min="1" max="<?= $movie['available_seats'] ?>" required>
                    <button type="submit" name="book_ticket">Book Ticket</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
