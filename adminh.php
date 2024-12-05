<?php
// Include database connection
include 'dbc.php';

$message = '';

// Handle form submission for adding, editing, or deleting hotels
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a new hotel
    if (isset($_POST['addHotel'])) {
        $hotelName = $_POST['hotelName'];
        $hotelLocation = $_POST['hotelLocation'];
        $hotelDetails = $_POST['hotelDetails'];

        $query = "INSERT INTO hotels (name, location, details) VALUES (?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sss", $hotelName, $hotelLocation, $hotelDetails);

        if ($stmt->execute()) {
            $message = "Hotel added successfully!";
        } else {
            $message = "Failed to add hotel.";
        }
    }

    // Handle editing and submitting updated hotel data
    if (isset($_POST['submit'])) {
        $hotelId = $_POST['hotelId'];
        $hotelName = $_POST['hotelName'];
        $hotelLocation = $_POST['hotelLocation'];
        $hotelDetails = $_POST['hotelDetails'];

        $query = "UPDATE hotels SET name = ?, location = ?, details = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssi", $hotelName, $hotelLocation, $hotelDetails, $hotelId);

        if ($stmt->execute()) {
            $message = "Hotel updated successfully!";
        } else {
            $message = "Failed to update hotel.";
        }
    }

    // Handle deleting a hotel
    if (isset($_POST['deleteHotel'])) {
        $hotelId = $_POST['hotelId'];

        $query = "DELETE FROM hotels WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $hotelId);

        if ($stmt->execute()) {
            $message = "Hotel deleted successfully!";
        } else {
            $message = "Failed to delete hotel.";
        }
    }
}

// Fetch all hotels for displaying in the table
$query = "SELECT * FROM hotels";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Hotels</title>
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
        <h1>Admin Panel - Manage Hotels</h1>

        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Table for Adding a New Hotel -->
        <h2>Add a New Hotel</h2>
        <form method="POST">
            <label for="hotelName">Hotel Name:</label>
            <input type="text" id="hotelName" name="hotelName" required>

            <label for="hotelLocation">Hotel Location:</label>
            <input type="text" id="hotelLocation" name="hotelLocation" required>

            <label for="hotelDetails">Hotel Details:</label>
            <textarea id="hotelDetails" name="hotelDetails" rows="5" ></textarea>

            <button type="submit" name="addHotel">Add Hotel</button>
        </form>

        <!-- Table for Managing Existing Hotels -->
        <h2>Manage Existing Hotels</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hotel Name</th>
                    <th>Location</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($hotel = $result->fetch_assoc()) : ?>
                    <tr class="hotel-<?php echo $hotel['id']; ?>">
                        <td><?php echo $hotel['id']; ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="hotelId" value="<?php echo $hotel['id']; ?>">
                                <input type="text" name="hotelName" value="<?php echo htmlspecialchars($hotel['name']); ?>" readonly class="hotel-name">
                        </td>
                        <td>
                                <input type="text" name="hotelLocation" value="<?php echo htmlspecialchars($hotel['location']); ?>" readonly class="hotel-location">
                        </td>
                        <td>
                                <textarea name="hotelDetails" rows="3" readonly class="hotel-details"><?php echo htmlspecialchars($hotel['details']); ?></textarea>
                        </td>
                        <td>
                            <button type="button" class="edit-btn" onclick="toggleEdit(<?php echo $hotel['id']; ?>)">Edit</button>
                            <button type="submit" name="submit" style="display:none;" class="save-btn">Save</button>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="hotelId" value="<?php echo $hotel['id']; ?>">
                                <button type="submit" name="deleteHotel" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Function to toggle the edit mode
        function toggleEdit(hotelId) {
            const hotelRow = document.querySelector('.hotel-' + hotelId);
            const nameField = hotelRow.querySelector('.hotel-name');
            const locationField = hotelRow.querySelector('.hotel-location');
            const detailsField = hotelRow.querySelector('.hotel-details');
            const editBtn = hotelRow.querySelector('.edit-btn');
            const saveBtn = hotelRow.querySelector('.save-btn');

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
