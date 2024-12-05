<?php
session_start();
include 'dbc.php'; // Include your database connection

// Assuming user_id is stored in the session after login
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Please log in to view your profile.";
    exit;
}

// Handle cancellation of bookings
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    $query = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $booking_id, $user_id);

    if ($stmt->execute()) {
        $message = "Booking cancelled successfully!";
    } else {
        $message = "Error cancelling booking: " . $stmt->error;
    }
}

// Handle updating of bookings
if (isset($_POST['edit_booking'])) {
    $booking_id = $_POST['booking_id'];
    $new_checkin = $_POST['checkin_date'];
    $new_checkout = $_POST['checkout_date'];

    $query = "UPDATE bookings SET checkin_date = ?, checkout_date = ? WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssii", $new_checkin, $new_checkout, $booking_id, $user_id);

    if ($stmt->execute()) {
        $message = "Booking updated successfully!";
    } else {
        $message = "Error updating booking: " . $stmt->error;
    }
}

// Fetch user's bookings
$query = "SELECT b.id, b.type, b.details, b.checkin_date, b.checkout_date, h.name AS hotel_name, m.name AS movie_name
          FROM bookings b
          LEFT JOIN hotels h ON b.details = h.id AND b.type = 'hotel'
          LEFT JOIN movies m ON b.details = m.id AND b.type = 'movie'
          WHERE b.user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
</head>
<body>
    <h1>Welcome to Your Profile</h1>

    <!-- Display Messages -->
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Bookings Section -->
    <h2>Your Bookings</h2>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Type</th>
                <th>Details</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ucfirst($row['type']); ?></td>
                    <td>
                        <?php
                        echo $row['type'] === 'hotel' ? $row['hotel_name'] : $row['movie_name'];
                        ?>
                    </td>
                    <td><?php echo $row['checkin_date'] ?? 'N/A'; ?></td>
                    <td><?php echo $row['checkout_date'] ?? 'N/A'; ?></td>
                    <td>
                        <!-- Cancel Booking -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="cancel_booking">Cancel</button>
                        </form>

                        <!-- Edit Booking -->
                        <?php if ($row['type'] === 'hotel'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="date" name="checkin_date" value="<?php echo $row['checkin_date']; ?>" required>
                                <input type="date" name="checkout_date" value="<?php echo $row['checkout_date']; ?>" required>
                                <button type="submit" name="edit_booking">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
    <?php endif; ?>
</body>
</html>
