<?php
session_start();
$error_message = "";
$success_message = "";

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

// Process verify code form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $code = $_POST['code'];

    // Check if the code is valid and not expired
    $sql = "SELECT * FROM password_resets WHERE email = '$email' AND code = '$code' AND expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Code is valid, redirect to reset_password.php with a token
        $_SESSION['reset_code'] = $code;
        header("Location: reset_password.php");
        exit();
    } else {
        $error_message = "Invalid or expired code. Please try again.";
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
    <title>Verify Code</title>
    <link rel="stylesheet" href="aut.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form p-3">
            <form action="verify_code.php" method="POST">
                <a href="index.php"><i class='bx bx-home-alt-2'></i></a>
                <div class="text-center">
                    <img src="img/MAPARCO.png" alt="Logo" class="logo">
                    <h5 class="mb-4"><a href="payroll/login.php">MAPARCO</a></h5>
                </div>
                <?php if ($error_message !== "") : ?>
                    <p style="color: red; text-align:center;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="code">Enter the 6-digit Code</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-key"></i></span>
                        </div>
                        <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" required>
                    </div>
                </div>
                <div class="link mt-3">
                    <button type="submit" class="btn btn-success btn-md" style="width:100%;">Verify Code</button>
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
