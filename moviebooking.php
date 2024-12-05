<?php
include 'dbc.php'; // Include your database connection
?>


<!DOCTYPE html>
<html>
<head>
    <title>Movie Booking System</title>
    <script>
        function showStep(step) {
            document.getElementById('step1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step2').style.display = step === 2 ? 'block' : 'none';
            document.getElementById('step3').style.display = step === 3 ? 'block' : 'none';
        }

        function showTrailer(trailerUrl) {
            document.getElementById('trailer').src = trailerUrl;
            document.getElementById('trailerModal').style.display = 'block';
        }

        function closeTrailer() {
            document.getElementById('trailerModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h1>Movie Booking System</h1>

    <!-- Step 1: Select Location -->
    <div id="step1">
        <h2>Step 1: Select Location</h2>
        <form method="POST">
            <label for="location">Location:</label>
            <select name="location" id="location" required>
                <option value="">--Select Location--</option>
                <?php
                $query = "SELECT * FROM locations";
                $result = $con->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="step1">Next</button>
        </form>
    </div>

    <!-- Step 2: Select Movie Hall -->
    <div id="step2" style="display: none;">
        <h2>Step 2: Select Movie Hall</h2>
        <form method="POST">
            <?php
            if (isset($_POST['step1'])) {
                $location_id = $_POST['location'];
                echo "<input type='hidden' name='location' value='{$location_id}'>";
            } else {
                $location_id = $_POST['location'] ?? '';
            }
            ?>
            <label for="hall">Movie Hall:</label>
            <select name="hall" id="hall" required>
                <option value="">--Select Movie Hall--</option>
                <?php
                $query = "SELECT * FROM movie_halls WHERE location_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $location_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="step2">Next</button>
        </form>
    </div>

    <!-- Step 3: Book Seats -->
    <div id="step3" style="display: none;">
        <h2>Step 3: Select Movie & Book Seats</h2>
        <form method="POST">
            <?php
            if (isset($_POST['step2'])) {
                $hall_id = $_POST['hall'];
                echo "<input type='hidden' name='hall' value='{$hall_id}'>";
            }
            ?>
            <label for="movie">Select Movie:</label>
            <select name="movie" id="movie" required>
                <option value="">--Select Movie--</option>
                <?php
                $query = "SELECT * FROM movies WHERE hall_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $hall_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}' data-trailer='{$row['trailer_url']}'>{$row['title']}</option>";
                }
                ?>
            </select>
            <button type="button" onclick="showTrailer(document.querySelector('#movie option:checked').getAttribute('data-trailer'))">View Trailer</button><br>

            <label for="name">Your Name:</label>
            <input type="text" name="name" id="name" required><br>

            <label for="email">Your Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="seats">Number of Seats:</label>
            <input type="number" name="seats" id="seats" min="1" max="10" required><br>

            <button type="submit" name="book">Book Now</button>
        </form>
    </div>

    <?php
    // Booking confirmation
    if (isset($_POST['book'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $movie_id = $_POST['movie'];
        $seats = $_POST['seats'];

        $query = "INSERT INTO bookings (user_name, user_email, movie_id, seats) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssii", $name, $email, $movie_id, $seats);

        if ($stmt->execute()) {
            echo "<h2>Booking Confirmed!</h2>";
            echo "Thank you for booking {$seats} seat(s) for the movie.<br>";
        } else {
            echo "<h2>Booking Failed!</h2>";
        }
    }

    // Show next step
    if (isset($_POST['step1'])) {
        echo "<script>showStep(2);</script>";
    } elseif (isset($_POST['step2'])) {
        echo "<script>showStep(3);</script>";
    }
    ?>

    <!-- Trailer Modal -->
    <div id="trailerModal" style="display:none;">
        <iframe id="trailer" width="560" height="315" src="" frameborder="0" allowfullscreen></iframe>
        <button onclick="closeTrailer()">Close</button>
    </div>
</body>
</html>
