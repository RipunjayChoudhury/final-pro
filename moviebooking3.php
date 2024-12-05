<?php
include 'dbc.php'; // Include your database connection

// Fetch locations
$query_locations = "SELECT DISTINCT location FROM mlocation";
$locations_result = $con->query($query_locations);
?>
<?php
include 'dbc.php'; // Include your database connection

$location = $_GET['location'];
$query_halls = "SELECT hall_name FROM mlocation WHERE location = ?";
$stmt_halls = $con->prepare($query_halls);
$stmt_halls->bind_param("s", $location);
$stmt_halls->execute();
$result = $stmt_halls->get_result();

$halls = [];
while ($row = $result->fetch_assoc()) {
    $halls[] = $row;
}

echo json_encode($halls);
?>
<?php
include 'dbc.php'; // Include your database connection

$hall = $_GET['hall'];
$query_movies = "SELECT id AS movie_id, name AS movie_name FROM movie WHERE hall_id IN (SELECT id FROM mlocation WHERE hall_name = ?)";
$stmt_movies = $con->prepare($query_movies);
$stmt_movies->bind_param("s", $hall);
$stmt_movies->execute();
$result = $stmt_movies->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode($movies);
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2 {
            margin: 0 0 15px;
            color: #333;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 800px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select, input[type="text"], input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
    <script>
        function loadHalls() {
            const location = document.getElementById('location').value;
            const hallSelect = document.getElementById('hall');
            hallSelect.innerHTML = '<option value="">--Select Hall--</option>';

            if (location) {
                fetch(`fetch_halls.php?location=${location}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(hall => {
                            const option = document.createElement('option');
                            option.value = hall.hall_name;
                            option.textContent = hall.hall_name;
                            hallSelect.appendChild(option);
                        });
                        document.getElementById('hallSection').style.display = 'block';
                    });
            } else {
                document.getElementById('hallSection').style.display = 'none';
                document.getElementById('movieSection').style.display = 'none';
                document.getElementById('bookingForm').style.display = 'none';
            }
        }

        function loadMovies() {
            const hall = document.getElementById('hall').value;
            const movieSelect = document.getElementById('movie');
            movieSelect.innerHTML = '<option value="">--Select Movie--</option>';

            if (hall) {
                fetch(`fetch_movies.php?hall=${hall}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(movie => {
                            const option = document.createElement('option');
                            option.value = movie.movie_id;
                            option.textContent = movie.movie_name;
                            movieSelect.appendChild(option);
                        });
                        document.getElementById('movieSection').style.display = 'block';
                    });
            } else {
                document.getElementById('movieSection').style.display = 'none';
                document.getElementById('bookingForm').style.display = 'none';
            }
        }

        function showBookingForm() {
            const movie = document.getElementById('movie').value;

            if (movie) {
                document.getElementById('bookingForm').style.display = 'block';
            } else {
                document.getElementById('bookingForm').style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1>Book Your Movie Tickets</h1>
    <div class="container">
        <div class="form-group">
            <label for="location">Select Location:</label>
            <select id="location" name="location" onchange="loadHalls()">
                <option value="">--Select Location--</option>
                <?php
                if ($locations_result->num_rows > 0) {
                    while ($location = $locations_result->fetch_assoc()) {
                        echo "<option value='{$location['location']}'>{$location['location']}</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group" id="hallSection" style="display: none;">
            <label for="hall">Select Hall:</label>
            <select id="hall" name="hall" onchange="loadMovies()">
                <option value="">--Select Hall--</option>
            </select>
        </div>

        <div class="form-group" id="movieSection" style="display: none;">
            <label for="movie">Select Movie:</label>
            <select id="movie" name="movie" onchange="showBookingForm()">
                <option value="">--Select Movie--</option>
            </select>
        </div>

        <div id="bookingForm" style="display: none;">
            <form method="POST" action="book.php">
                <input type="hidden" name="movie_id" id="hiddenMovieId">
                <label for="user_name">Your Name:</label>
                <input type="text" name="user_name" id="user_name" required>

                <label for="seats_to_book">Number of Seats:</label>
                <input type="number" name="seats_to_book" id="seats_to_book" min="1" required>

                <button type="submit" name="book_ticket">Book Ticket</button>
            </form>
        </div>
    </div>
</body>
</html>


