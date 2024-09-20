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
    <title>Customer Login</title>
    <link rel="stylesheet" href="authi.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form p-3">
            <form action="login.php" method="POST">
                <a href="index.php"><i class='bx bx-home-alt-2'></i></a>
                <div class="text-center">
                    <img src="img/MAPARCO.png" alt="Logo" class="logo">
                    <h5 class="mb-4"><a href="payroll/login.php">MAPARCO</a></h5>
                </div>
                <?php if ($error_message !== "") : ?>
                    <p style="color: red; text-align:center;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Please enter your E-mail">
                    </div>
                </div>
                <div class="form-group mt-2">
                    <label for="password">Password</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-secondary"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Please enter your password" required>
                    </div>
                    <div class="forgot mt-2">
                        <a href="forgot_password.php">Forgot your password?</a>
                    </div>
                    <div class="link mt-3">
                        <button type="submit" class="btn btn-success btn-md">Login</button>
                        <div class="text-center mt-2">
                            <p>Don't have an account? <a href="register.php">Register</a> here.</p>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>
    <style>
        .link {
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 767.98px) {
            .form {
                min-width: 340px;
                /* 342px reduced by 30% */
                max-width: 350px;
                /* 350px reduced by 30% */
                background-color: #fff;
                border: 0.7px solid #ccc;
                /* 1px reduced by 30% */
                border-radius: 3.5px;
                position: absolute;
                top: 15%;
            }

            .text-center p,
            .forgot a{
                font-size: 13px;
            }

            input[type="password"],
            input[type="email"],
            input[type="text"],
            input[type="tel"] {
                font-size: 13px;
                /* 12px reduced by 30% */
                border: transparent;
            }

            .form h5 {
                text-align: center;
                margin-top: 0;
                font-weight: bold;
                font-size: 22px;
            }

            .form a i {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 25px;
            }

            form {
                padding: 1px 1px 0px;
                line-height: 2em;
            }

            label {
                font-size: 13px;
            }

            .input-group i {
                font-size: 13px;
                /* 13px reduced by 30% */
            }

            .btn.btn-success.btn-md {
                font-size: 13px;
                padding: 10px 20px;
            }
        }
    </style>
</body>

</html>