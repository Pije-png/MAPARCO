<?php
session_start();
$error_message = "";
$success_message = "";

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

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Process reset password form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_SESSION['reset_code'];
    $email = $_SESSION['reset_email'];
    $new_password = sanitize_input($_POST['new_password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    // Validate that the new passwords match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Check if the code is still valid
        $sql = "SELECT * FROM password_resets WHERE email = '$email' AND code = '$code' AND expires_at > NOW()";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Update the user's password in the customers table
            $sql_update_password = "UPDATE customers SET Password = '$hashed_password' WHERE Email = '$email'";
            if ($conn->query($sql_update_password) === TRUE) {
                // Delete the reset code after successful password reset
                $sql_delete_code = "DELETE FROM password_resets WHERE email = '$email'";
                $conn->query($sql_delete_code);

                $success_message = "Your password has been successfully reset. You can now <a href='login.php'>login</a>.";
            } else {
                $error_message = "Something went wrong. Please try again.";
            }
        } else {
            $error_message = "Invalid or expired code. Please try resetting your password again.";
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="aut.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form p-3">
            <form action="reset_password.php" method="POST">
                <a href="index.php"><i class='bx bx-home-alt-2'></i></a>
                <div class="text-center">
                    <img src="img/MAPARCO.png" alt="Logo" class="logo">
                    <h5 class="mb-4"><a href="payroll/login.php">MAPARCO</a></h5>
                </div>
                <?php if ($error_message !== "") : ?>
                    <p style="color: red; text-align:center;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <?php if ($success_message !== "") : ?>
                    <p style="color: green; text-align:center;"><?php echo $success_message; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="link mt-3">
                    <button type="submit" class="btn btn-success btn-md" style="width:100%;">Reset Password</button>
                </div>
                <div class="text-center mt-2">
                    <p>Remembered your password? <a href="login.php">Login</a> here.</p>
                </div>
            </form>
        </div>
    </div>
    <style>
        /* Your existing CSS for form styling goes here */
    </style>
</body>

</html>
