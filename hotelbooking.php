<?php
include 'dbc.php'; // Include your database connection
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hotel Booking System</title>

    <script>
        function showStep(step) {
            document.getElementById('step1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step2').style.display = step === 2 ? 'block' : 'none';
            document.getElementById('step3').style.display = step === 3 ? 'block' : 'none';
        }
    </script>
    
</head>
<body>
    <h1>Hotel Booking System</h1>

    <!-- Step 1: Select Location -->
    <div id="step1">
        <h2>Step 1: Select Location</h2>
        <form method="POST">
            <label for="location">Location:</label>
            <select name="location" id="location" required>
                <option value="">--Select Location--</option>
                <?php
                $query = "SELECT DISTINCT location FROM hotels";
                $result = mysqli_query($con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['location']}'>{$row['location']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="step1">Next</button>
        </form>
    </div>

    <!-- Step 2: Select Hotel -->
    <div id="step2" style="display: none;">
        <h2>Step 2: Select Hotel</h2>
        <form method="POST">
            <?php
            if (isset($_POST['step1'])) {
                $location = $_POST['location'];
                echo "<input type='hidden' name='location' value='{$location}'>";
            } else {
                $location = $_POST['location'] ?? '';
            }
            ?>
            <label for="hotel">Hotel:</label>
            <select name="hotel" id="hotel" required>
                <option value="">--Select Hotel--</option>
                <?php
                $query = "SELECT * FROM hotels WHERE location = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("s", $location);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="step2">Next</button>
        </form>
    </div>

    <!-- Step 3: Book Room -->
    <div id="step3" style="display: none;">
    <h2>Step 3: Book a Room</h2>
    <form method="POST">
        <?php
        if (isset($_POST['step2'])) {
            $hotel = $_POST['hotel'];
            $location = $_POST['location'];
            echo "<input type='hidden' name='hotel' value='{$hotel}'>";
            echo "<input type='hidden' name='location' value='{$location}'>";
        }
        ?>
        <label for="name">Your Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="roomtype">Room Type:</label>
        <select name="roomtype" id="rt" onchange="calculatePrice()" required>
            <option value="">--Select Room Type--</option>
            <option value="Single">Single</option>
            <option value="Double">Double</option>
            <option value="Suite">Suite</option>
        </select><br>

        <label for="price">Price:</label>
        <input type="text" name="price" id="p" readonly required><br>

        <label for="cin">Check-In Date:</label>
        <input type="date" name="cin" id="cin" required><br>

        <label for="cout">Check-Out Date:</label>
        <input type="date" name="cout" id="cout" required><br>

        <button type="submit" name="step3">Book Now</button>
    </form>
</div>

<script>
    function calculatePrice() {
        // Get the selected room type
        var roomType = document.getElementById("rt").value;
        var price;

        // Determine price based on room type
        if (roomType === "Single") {
            price = 1500;
        } else if (roomType === "Double") {
            price = 2000;
        } else if (roomType === "Suite") {
            price = 4500;
        } else {
            price = 0; // Default if no selection
        }

        // Set the price in the input field
        document.getElementById("p").value = price;
    }
</script>

    <?php
    // Display next step based on form submission
    if (isset($_POST['step1'])) {
        echo "<script>showStep(2);</script>";
    } elseif (isset($_POST['step2'])) {
        echo "<script>showStep(3);</script>";
    } elseif (isset($_POST['step3'])) {
        // Process the booking
        
            // Process the booking
            $name = $_POST['name'];
            $email = $_POST['email'];
            $roomtype = $_POST['roomtype'];
            $price = $_POST['price'];
            $cin = date('Y-m-d', strtotime($_POST['cin']));
            $cout = date('Y-m-d', strtotime($_POST['cout']));
        
            $query = "INSERT INTO bhotal (location, hotel, name, email, roomtype, price, cin, cout)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssssssss", $_POST['location'], $_POST['hotel'], $name, $email, $roomtype, $price, $cin, $cout);
        
            if ($stmt->execute()) {
                echo "<h2>Booking Confirmed!</h2>";
                echo "Thank you for booking at {$_POST['hotel']} in {$_POST['location']}.<br>";
            } else {
                echo "<h2>Booking Failed</h2>";
            }
        }
        
    ?>
</body>
</html>
