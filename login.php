<?php
session_start();
$error_message = ""; // Initialize error message

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

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user is a customer
    $sql_customer = "SELECT * FROM customers WHERE Email = '$email'";
    $result_customer = $conn->query($sql_customer);

    if ($result_customer->num_rows == 1) {
        $row_customer = $result_customer->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row_customer['Password'])) {
            // Password is correct, set session variables for customer and redirect to customer dashboard
            $_SESSION['customer_id'] = $row_customer['CustomerID'];
            $_SESSION['customer_email'] = $row_customer['Email'];
            header("Location: users/dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        // Check if the user is an admin
        $sql_admin = "SELECT * FROM admins WHERE Email = '$email' AND Is_Admin = 1";
        $result_admin = $conn->query($sql_admin);

        if ($result_admin->num_rows == 1) {
            $row_admin = $result_admin->fetch_assoc();
            // Verify the password
            if (password_verify($password, $row_admin['Password'])) {
                // Password is correct, set session variables for admin and redirect to admin dashboard
                $_SESSION['admin_id'] = $row_admin['ID'];
                $_SESSION['admin_email'] = $row_admin['Email'];
                header("Location: admin/home.php");
                exit();
            } else {
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            $error_message = "Please enter valid information";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="auth.css">
    <title>Customer Login</title>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="text-center">
                <img src="img/MAPARCO.png" alt="Logo">
                <h5 class="text-success mb-2"><a href="payroll/login.php">MAPARCO</a></h5>
            </div>

            <?php if ($error_message !== "") : ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder=" " required>
                    <label for="email">Email address</label>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Password</label>
                </div>

                <div class="forgot">
                    <a href="forgot_password.php">Forgot your password?</a>
                </div>

                <button type="submit" class="btn btn-success login-btn">Login</button>

                <div class="register">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>