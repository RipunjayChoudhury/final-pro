<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Easy Signup</title>
    <link rel="stylesheet" href="bookeasy_signup.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1, h4 {
            margin: 0 0 15px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        th {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Easy</h1>
        <h4>Register yourself</h4>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
            <table>
                <tr>
                    <th>First Name:</th>
                    <td><input type="text" name="firstn" placeholder="" required></td>
                </tr>
                <tr>
                    <th>Last Name:</th>
                    <td><input type="text" name="lastn" placeholder="" required></td>
                </tr>
                <tr>
                    <th>Phone No.:</th>
                    <td><input type="number" name="pno" placeholder="1234567890" required></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><input type="email" name="mail" placeholder="@gmail.com" required></td>
                </tr>
                <tr>
                    <th>Password:</th>
                    <td><input type="password" name="pword" placeholder="enter_password" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="Register">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>

<?php
session_start(); // Start the session
include('dbc.php'); // Include your database connection

if (isset($_POST['submit'])) {
    $fn = $_POST["firstn"];
    $ln = $_POST["lastn"];
    $em = $_POST["mail"];
    $pn = $_POST["pno"];
    $ps = $_POST["pword"]; // Hash the password

    $inq = "INSERT INTO users (fname, lname, email, phn, pass) VALUES ('$fn', '$ln', '$em', '$pn', '$ps')";

    $qu = mysqli_query($con, $inq);
    if ($qu) {
        $_SESSION['username'] = $fn; // Set session variable
        echo '<script>alert("Registration successful");</script>';
    } else {
        echo '<script>alert("Error: ' . mysqli_error($con) . '");</script>';
    }
}
?>
