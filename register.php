<?php
// Include your database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MAPARCO";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set create_on timestamp
    $create_on = date('Y-m-d H:i:s');

    // Prepare SQL statement to insert customer data into the database
    $sql = "INSERT INTO customers (Name, Email, Password, create_on)
            VALUES ('$name', '$email', '$hashed_password', '$create_on')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="auth.css">
    <title>Customer Registration</title>
</head>

<body>
    <div class="container">
        <div class="card register">
            <div class="text-center mb-2">  
                <img src="img/MAPARCO.png" alt="Logo">
                <h5 class="text-primary">Create an Account</h5>
            </div>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder=" " required>
                    <label for="name">Full Name</label>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder=" " required>
                    <label for="email">Email address</label>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" minlength="8" placeholder=" " required>
                    <label for="password">Password</label>
                </div>

                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" minlength="8" placeholder=" " required>
                    <label for="confirm_password">Confirm Password</label>
                </div>

                <button type="submit" class="btn btn-success register-btn">Register</button>

                <div class="login">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>