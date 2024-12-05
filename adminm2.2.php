<?php
include 'dbc.php'; // Include your database connection

// Add a new movie hall with location
if (isset($_POST['add_hall'])) {
    // Sanitize and validate inputs
    $location_name = trim($_POST['location_name']);
    $hall_name = trim($_POST['hall_name']);
    $details = trim($_POST['details']);
    $seat = trim($_POST['seat']);

    // Ensure required fields are filled
    if (empty($location_name) || empty($hall_name) || empty($seat)) {
        echo "<p>All fields are required.</p>";
    } else {
        $con->begin_transaction(); // Start transaction

        try {
            // Check if location already exists in `mlocation`
            $query_location = "SELECT id FROM mlocation WHERE location = ?";
            $stmt_location = $con->prepare($query_location);
            $stmt_location->bind_param("s", $location_name);
            $stmt_location->execute();
            $stmt_location->store_result();

            if ($stmt_location->num_rows > 0) {
                $stmt_location->bind_result($location_id);
                $stmt_location->fetch();
            } else {
                // Insert new location into `mlocation`
                $insert_location = "INSERT INTO mlocation (location) VALUES (?)";
                $stmt_insert = $con->prepare($insert_location);
                $stmt_insert->bind_param("s", $location_name);

                if ($stmt_insert->execute()) {
                    $location_id = $stmt_insert->insert_id;
                } else {
                    throw new Exception("Error adding location: " . $stmt_insert->error);
                }
            }

            // Insert movie hall into `movie_hall`
            $query_hall = "INSERT INTO movie_hall (name, seats, location_id, details) VALUES (?, ?, ?, ?)";
            $stmt_hall = $con->prepare($query_hall);
            $stmt_hall->bind_param("siss", $hall_name, $seat, $location_id, $details);

            if ($stmt_hall->execute()) {
                echo "<p>Movie Hall added successfully with location!</p>";
            } else {
                throw new Exception("Error adding movie hall: " . $stmt_hall->error);
            }

            $con->commit(); // Commit transaction
        } catch (Exception $e) {
            $con->rollback(); // Rollback on error
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }
}

// Add a new movie
if (isset($_POST['add_movie'])) {
    $movie_title = trim($_POST['movie_title']);
    $hall_id = trim($_POST['hall_id']);
    $trailer_url = trim($_POST['trailer_url']);
    $movie_details = trim($_POST['movie_details']);

    // Insert movie into `movie` table
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }

        h1, h2 {
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, select, textarea, button {
            margin: 5px 0 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Movie Booking System</h1>

        <!-- Add New Movie Hall with Location Form -->
        <h2>Add New Movie Hall</h2>
        <form method="POST">
            <label for="location_name">Location Name:</label>
            <input type="text" name="location_name" id="location_name" required>

            <label for="hall_name">Hall Name:</label>
            <input type="text" name="hall_name" id="hall_name" required>

            <label for="details">Hall Details:</label>
            <input type="text" name="details" id="details">

            <label for="seat">Seat Count:</label>
            <input type="number" name="seat" id="seat" required>

            <button type="submit" name="add_hall">Add Movie Hall</button>
        </form>

        <hr>

        <!-- Add New Movie Form -->
        <h2>Add New Movie</h2>
        <form method="POST">
            <label for="movie_title">Movie Title:</label>
            <input type="text" name="movie_title" id="movie_title" required>

            <label for="hall_id">Select Movie Hall:</label>
            <select name="hall_id" id="hall_id" required>
                <option value="">--Select Movie Hall--</option>
                <?php
                // Fetch all movie halls
                $query_hall = "SELECT id, name FROM movie_hall";
                $halls = $con->query($query_hall);
                while ($hall = $halls->fetch_assoc()) {
                    echo "<option value='{$hall['id']}'>{$hall['name']}</option>";
                }
                ?>
            </select>

            <label for="trailer_url">Trailer URL:</label>
            <input type="url" name="trailer_url" id="trailer_url" required>

            <label for="movie_details">Movie Details:</label>
            <textarea name="movie_details" id="movie_details" required></textarea>

            <button type="submit" name="add_movie">Add Movie</button>
        </form>
    </div>
</body>
</html>
