<?php
// Include database connection
include 'dbc.php';

$message = '';

// Handle form submission for adding, editing, or deleting movies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a new movie
    if (isset($_POST['addMovie'])) {
        $movieName = $_POST['movieName'];
        $movieLocation = $_POST['movieLocation'];
        $movieDetails = $_POST['movieDetails'];

        $query = "INSERT INTO movie (name, location, details) VALUES (?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sss", $movieName, $movieLocation, $movieDetails);

        if ($stmt->execute()) {
            $message = "Movie added successfully!";
        } else {
            $message = "Failed to add movie.";
        }
    }

    // Handle editing and submitting updated movie data
    if (isset($_POST['submit'])) {
        $movieId = $_POST['movieId'];
        $movieName = $_POST['movieName'];
        $movieLocation = $_POST['movieLocation'];
        $movieDetails = $_POST['movieDetails'];

        $query = "UPDATE movie SET name = ?, location = ?, details = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssi", $movieName, $movieLocation, $movieDetails, $movieId);

        if ($stmt->execute()) {
            $message = "Movie updated successfully!";
        } else {
            $message = "Failed to update movie.";
        }
    }

    // Handle deleting a movie
    if (isset($_POST['deleteMovie'])) {
        $movieId = $_POST['movieId'];

        $query = "DELETE FROM movies WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $movieId);

        if ($stmt->execute()) {
            $message = "Movie deleted successfully!";
        } else {
            $message = "Failed to delete movie.";
        }
    }
}

// Fetch all movies for displaying in the table
$query = "SELECT * FROM movie";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Movies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        h1, h2 {
            text-align: center;
        }
        .message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
        form {
            margin: 20px 0;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .edit-mode input, .edit-mode textarea {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Manage Movies</h1>

        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Table for Adding a New Movie -->
        <h2>Add a New Movie</h2>
        <form method="POST">
            <label for="movieName">Movie Name:</label>
            <input type="text" id="movieName" name="movieName" required>

            <label for="movieLocation">Movie Location:</label>
            <input type="text" id="movieLocation" name="movieLocation" required>

            <label for="movieDetails">Movie Details:</label>
            <textarea id="movieDetails" name="movieDetails" rows="5" required></textarea>

            <button type="submit" name="addMovie">Add Movie</button>
        </form>

        <!-- Table for Managing Existing Movies -->
        <h2>Manage Existing Movies</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Movie Name</th>
                    <th>Location</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($movie = $result->fetch_assoc()) : ?>
                    <tr class="movie-<?php echo $movie['id']; ?>">
                        <td><?php echo $movie['id']; ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="movieId" value="<?php echo $movie['id']; ?>">
                                <input type="text" name="movieName" value="<?php echo htmlspecialchars($movie['name']); ?>" readonly class="movie-name">
                        </td>
                        <td>
                                <input type="text" name="movieLocation" value="<?php echo htmlspecialchars($movie['location']); ?>" readonly class="movie-location">
                        </td>
                        <td>
                                <textarea name="movieDetails" rows="3" readonly class="movie-details"><?php echo htmlspecialchars($movie['details']); ?></textarea>
                        </td>
                        <td>
                            <button type="button" class="edit-btn" onclick="toggleEdit(<?php echo $movie['id']; ?>)">Edit</button>
                            <button type="submit" name="submit" style="display:none;" class="save-btn">Save</button>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="movieId" value="<?php echo $movie['id']; ?>">
                                <button type="submit" name="deleteMovie" onclick="return confirm('Are you sure you want to delete this movie?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Function to toggle the edit mode
        function toggleEdit(movieId) {
            const movieRow = document.querySelector('.movie-' + movieId);
            const nameField = movieRow.querySelector('.movie-name');
            const locationField = movieRow.querySelector('.movie-location');
            const detailsField = movieRow.querySelector('.movie-details');
            const editBtn = movieRow.querySelector('.edit-btn');
            const saveBtn = movieRow.querySelector('.save-btn');

            if (nameField.readOnly) {
                // Switch to edit mode
                nameField.readOnly = false;
                locationField.readOnly = false;
                detailsField.readOnly = false;
                saveBtn.style.display = 'inline-block'; // Show save button
                editBtn.textContent = 'Cancel'; // Change button text to Cancel
            } else {
                // Switch back to view mode
                nameField.readOnly = true;
                locationField.readOnly = true;
                detailsField.readOnly = true;
                saveBtn.style.display = 'none'; // Hide save button
                editBtn.textContent = 'Edit'; // Reset button text
            }
        }
    </script>
</body>
</html>
