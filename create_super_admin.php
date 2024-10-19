<?php
// Include your database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maparco_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if the email already exists
    $sql_check = "SELECT * FROM admins WHERE Email = '$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $error_message = "An account with this email already exists.";
    } else {
        // Insert new super admin into the database
        $sql = "INSERT INTO admins (Username, Password, Email, Full_Name, Is_Admin, Is_SuperAdmin, photo) 
                VALUES ('$username', '$hashed_password', '$email', '$full_name', 1, 1, 'default_photo.jpg')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Super admin account created successfully.";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Super Admin Account</title>
</head>
<body>
    <h2>Create Super Admin Account</h2>
    <?php if ($error_message) { echo "<p style='color: red;'>$error_message</p>"; } ?>
    <?php if ($success_message) { echo "<p style='color: green;'>$success_message</p>"; } ?>

    <form action="" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Create Super Admin">
    </form>
</body>
</html>