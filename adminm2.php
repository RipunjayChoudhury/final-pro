<?php
include 'dbc.php'; // Include your database connection

// Add a new movie hall with location
if (isset($_POST['add_hall'])) {
    // Sanitize and validate inputs
    $location_name = trim($_POST['location_name']);
    $hall_name = trim($_POST['hall_name']);
    $details = trim($_POST['details']);
    $seat = trim($_POST['seat']);

    // Ensure all fields are filled
    if (empty($location_name) || empty($hall_name) || empty($seat)) {
        echo "<p>All fields are required.</p>";
    } else {
        // Insert movie hall into mlocation table with location, seats, and details
        $query = "INSERT INTO mlocation (location, hall_name, seats, details) VALUES (?, ?, ?, ?)";



        // Fetch the location ID for the given location name
        $query_location = "SELECT id FROM mlocation WHERE hall_name = ?";
        $stmt_location = $con->prepare($query_location);
        $stmt_location->bind_param("s", $location_name);
        $stmt_location->execute();
        $stmt_location->store_result();
        $stmt_location->bind_result($location_id);
        $stmt_location->fetch();

        if ($stmt_location->num_rows > 0) {
            $stmt_hall = $con->prepare($query);
            $stmt_hall->bind_param("siss", $hall_name, $seat, $location_id, $details);

            if ($stmt_hall->execute()) {
                echo "<p>Movie Hall added successfully with location!</p>";
            } else {
                echo "<p>Error adding movie hall: " . $stmt_hall->error . "</p>";
            }
        } else {
            echo "<p>Location does not exist. Please add the location first.</p>";
        }
    }
}

// Add a new movie
if (isset($_POST['add_movie'])) {
    $movie_title = $_POST['movie_title'];
    $hall_id = $_POST['hall_id'];
    $trailer_url = $_POST['trailer_url'];
    $movie_details = $_POST['movie_details'];

    // Insert movie into movies table
    $query_movie = "INSERT INTO movie (name, hall_id, trailer_url, details) VALUES (?, ?, ?, ?)";
    $stmt_movie = $con->prepare($query_movie);
    $stmt_movie->bind_param("ssss", $movie_title, $hall_id, $trailer_url, $movie_details);
    
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
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }

        h1, h2 {
            margin: 0 0 15px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, select, textarea {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
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
            <input type="text" name="location_name" id="location_name" required><br>

            <label for="hall_name">Hall Name:</label>
            <input type="text" name="hall_name" id="hall_name" required><br>

            <label for="details">Hall Details:</label>
            <input type="text" name="details" id="details" ><br>

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
                // Fetch all movie halls from the database
                $query_hall = "SELECT * FROM mlocation";
                $halls = $con->query($query_hall);
                while ($hall = $halls->fetch_assoc()) {
                    echo "<option value='{$hall['id']}'>{$hall['hall_name']} ({$hall['location']})</option>";
                }
                ?>
            </select><br>

            <label for="trailer_url">Trailer URL:</label>
            <input type="url" name="trailer_url" id="trailer_url" required><br>

            <label for="movie_details">Movie Details:</label>
            <textarea name="movie_details" id="movie_details" required></textarea><br>

            <button type="submit" name="add_movie">Add Movie</button>
        </form>
    </div>
</body>
</html>
