<?php
include 'dbc.php'; // Include your database connection

// Add a new movie hall with location
if (isset($_POST['add_hall'])) {
    // Sanitize and validate inputs
    $location_name = trim($_POST['location_name']);
    $hall_name = trim($_POST['hall_name']);
    $detail = trim($_POST['details']);
    $seat = trim($_POST['seat']);

    // Ensure all fields are filled
    if (empty($location_name) || empty($hall_name) || empty($seat)) {
        echo "<p>All fields are required.</p>";
    } else {
        // Insert movie hall into movie_hall table
        $query = "INSERT INTO mlocation (location, hall_name, seats, details) VALUES (?, ?, ?, ?)";

        $stmt_hall = $con->prepare($query);

        // Bind parameters (4 parameters: hall_name, seats, location_name, and details)
        $stmt_hall->bind_param("siss", $hall_name, $seat, $location_name, $detail);

        if ($stmt_hall->execute()) {
            echo "<p>Movie Hall added successfully with location!</p>";
        } else {
            echo "<p>Error adding movie hall: " . $stmt_hall->error . "</p>";
        }
    }
}

// Add a new movie
if (isset($_POST['add_movie'])) {
    $movie_title = $_POST['movie_title'];
    $hall_id = $_POST['hall_id'];
    $trailer_url = $_POST['trailer_url'];
    $movie_details = $_POST['movie_details'];

    // Insert movie into movie table
    $query_movie = "INSERT INTO movie (name, hall_id, trailer_url, details) VALUES (?, ?, ?, ?)";
    $stmt_movie = $con->prepare($query_movie);
    $stmt_movie->bind_param("siss", $movie_title, $hall_id, $trailer_url, $movie_details);

    if ($stmt_movie->execute()) {
        echo "<p>Movie added successfully!</p>";
    } else {
        echo "<p>Error adding movie: " . $stmt_movie->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Movie Booking System</title>
</head>
<body>
    <h1>Admin Panel - Movie Booking System</h1>

    <!-- Add New Movie Hall with Location Form -->
    <h2>Add New Movie Hall</h2>
    <form method="POST">
        <label for="location_name">Location Name:</label>
        <input type="text" name="location_name" id="location_name" required><br>

        <label for="hall_name">Hall Name:</label>
        <input type="text" name="hall_name" id="hall_name" required><br>

        <label for="details">Hall Details:</label>
        <input type="text" name="details" id="details"><br>

        <label for="seat">Seat Details:</label>
        <input type="number" name="seat" id="seat" required><br>

        <button type="submit" name="add_hall">Add Movie Hall and Location</button>
    </form>

    <hr>

    <!-- Add New Movie Form -->
    <h2>Add New Movie</h2>
    <form method="POST">
        <label for="movie_title">Movie Title:</label>
        <input type="text" name="movie_title" id="movie_title" required><br>

        <label for="hall_id">Select Movie Hall:</label>
        <select name="hall_id" id="hall_id" required>
            <option value="">--Select Movie Hall--</option>
            <?php
            // Fetch all movie halls from the movie_hall table
            $query_hall = "SELECT id, name FROM movie_hall";
            $halls = $con->query($query_hall);
            while ($hall = $halls->fetch_assoc()) {
                echo "<option value='{$hall['id']}'>{$hall['name']}</option>";
            }
            ?>
        </select><br>

        <label for="trailer_url">Trailer URL:</label>
        <input type="url" name="trailer_url" id="trailer_url" required><br>

        <label for="movie_details">Movie Details:</label>
        <textarea name="movie_details" id="movie_details" required></textarea><br>

        <button type="submit" name="add_movie">Add Movie</button>
    </form>
</body>
</html>
