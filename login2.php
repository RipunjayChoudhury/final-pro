<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Easy Login</title>
    <link rel="stylesheet" href="bookeasy_login.css">
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
        <h4>Login</h4>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
            <table>
                <tr>
                    <th>Email:</th>
                    <td><input type="text" name="email" placeholder="@gmail.com" required></td>
                </tr>
                <tr>
                    <th>Password:</th>
                    <td><input type="password" name="pass" placeholder="enter_password" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="Login">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>

<?php
session_start();
include 'dbc.php'; // Include your database connection

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['pass'])) {
            $_SESSION['username'] = $user['fname']; // Save user's first name in session
            echo "<script>
                alert('Login successful!');
                window.location.href='home2.php';
            </script>";
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Invalid email');</script>";
    }
}
?>
