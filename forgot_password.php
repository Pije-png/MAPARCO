<?php
session_start();
$error_message = "";
$success_message = "";

// Include your database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MAPARCO";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a random 6-digit code
function generate_code() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Function to send the code to the user's email
function send_code($email, $code) {
    $subject = "Password Reset Code";
    $message = "Your password reset code is: " . $code;
    $headers = "From: no-reply@maparco.shop";
    
    mail($email, $subject, $message, $headers);
}

// Process forgot password form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the user exists in the customers table
    $sql_customer = "SELECT * FROM customers WHERE Email = '$email'";
    $result_customer = $conn->query($sql_customer);

    if ($result_customer->num_rows == 1) {
        $code = generate_code();

        // Save the code in the database
        $expires_at = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        $sql_insert_code = "INSERT INTO password_resets (email, code, expires_at) VALUES ('$email', '$code', '$expires_at')
                            ON DUPLICATE KEY UPDATE code='$code', expires_at='$expires_at'";

        if ($conn->query($sql_insert_code) === TRUE) {
            // Send the code to the user's email
            send_code($email, $code);

            // Redirect to the verify_code.php page
            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
            exit();
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
    } else {
        $error_message = "No account found with that email.";
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="authi.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form p-3">
            <form action="forgot_password.php" method="POST">
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
                    <label for="email">Email</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Please enter your E-mail" required>
                    </div>
                </div>
                <div class="link mt-3">
                    <button type="submit" class="btn btn-success btn-md" style="width:100%;">Send Code</button>
                    <div class="text-center mt-2">
                        <p>Remembered your password? <a href="login.php">Login</a> here.</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        /* Your existing CSS for form styling goes here */
    </style>
</body>

</html>
