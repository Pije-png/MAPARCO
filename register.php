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
    <title>Customer Registration</title>
    <link rel="stylesheet" href="authi.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form p-3">
            <form action="register.php" method="POST">
                <div class="text-center">
                    <img src="img/MAPARCO.png" alt="MAPARCO Logo" class="logo" width="110px">
                    <h5 class="mb-4">Create an Account</h5>
                </div>
                <div class="form-group mt-1">
                    <label for="name">Full Name</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="text-secondary input-group-text"><i class="fa-solid fa-user"></i></span>
                        </div>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <label for="email">Email</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="text-secondary input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Please enter your E-mail" required>
                    </div>
                </div>

                <div class="form-group mt-1">
                    <label for="password">Password</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="text-secondary input-group-text"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters with a number and a letter" required>
                    </div>
                </div>

                <div class="form-group mt-1">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group border border-1 rounded">
                        <div class="input-group-prepend">
                            <span class="text-secondary input-group-text"><i class="fa-solid fa-right-to-bracket"></i></span>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" class="form-control" required>
                    </div>
                </div>

                <div class="link">
                    <input type="submit" value="Register" class="btn btn-success btn-md mt-3">
                    <div class="text-center mt-3">
                        <p>If you already have an account, <a href="login.php">login</a> here.</p>
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
                top: 10%;
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