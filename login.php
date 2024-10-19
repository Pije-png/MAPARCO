<?php
session_start();
$error_message = ""; // Initialize error message

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
        // Check if the user is a super admin
        $sql_superadmin = "SELECT * FROM admins WHERE Email = '$email' AND Is_SuperAdmin = 1";
        $result_superadmin = $conn->query($sql_superadmin);

        if ($result_superadmin->num_rows == 1) {
            $row_superadmin = $result_superadmin->fetch_assoc();
            // Verify the password
            if (password_verify($password, $row_superadmin['Password'])) {
                // Password is correct, set session variables for super admin and redirect to super admin dashboard
                $_SESSION['super_admin_id'] = $row_superadmin['ID'];
                $_SESSION['super_admin_email'] = $row_superadmin['Email'];
                header("Location: admin/home.php");
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
                $error_message = "Please enter valid information.";
            }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="aut.css">
    <title>Customer Login</title>
</head>

<body>
    <div class="container">
        <div class="column">
            <div class="card">
                <div class="text-center mb-3">
                    <a href="index.php"> <img src="img/MAPARCO.png" alt="Logo"></a>
                    <h5 class="text-primary mb-2"><a href="payroll/login.php">MAPARCO</a><small>&trade;</small></h5>
                </div>

                <?php if ($error_message !== "") : ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <span class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" placeholder=" " required>
                        <label for="email">Email address</label>
                    </div>

                    <div class="form-group">
                        <span class="input-icon">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" id="password" name="password" placeholder=" " required>
                        <label for="password">Password</label>
                    </div>
                    <div class="forgot">
                        <a href="forgot_password.php">Forgot your password?</a>
                    </div>

                    <button type="submit" class="btn btn-success login-btn">Login</button>
                </form>
            </div>
            <div class="border mt-3">
                <div class="register pt-3">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>