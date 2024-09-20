<?php
session_start();

// Function to redirect to the login page
function redirectToLogin()
{
    header("Location: login.php");
    exit;
}

// Check if the user is an admin
if (isset($_SESSION['admin_id'])) {
    // If admin, confirm logout as admin
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'admin') {
        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the admin session
        session_destroy();

        // Redirect to the login page
        redirectToLogin();
    }
} elseif (isset($_SESSION['customer_id'])) {
    // If regular user, confirm logout as user
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'user') {
        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the user session
        session_destroy();

        // Redirect to the login page
        redirectToLogin();
    }
} else {
    // If no session is active, redirect to the login page
    redirectToLogin();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .confirmation-dialog h3 {
            margin-top: 0;
        }

        .confirmation-dialog button {
            margin-top: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }
          @media (max-width: 510px) {
              h3{
                  font-size: 20px;
              }
          }
    </style>
</head>

<body>

    <div class="confirmation-dialog" id="confirmationDialog">
        <h3>Are you sure you want to log out?</h3>
        <button id="confirmLogout" class="btn btn-danger">Yes</button>
        <button id="cancelLogout" class="btn">Cancel</button>
    </div>

    <script>
        // Show the confirmation dialog when the page loads
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("confirmationDialog").style.display = "block";
        });

        // Add event listeners for confirmation and cancellation
        document.getElementById("confirmLogout").addEventListener("click", function() {
            // Redirect to the appropriate logout URL based on user type
            <?php
            if (isset($_SESSION['admin_id'])) {
                echo 'window.location.href = "logout.php?confirm=admin";';
            } elseif (isset($_SESSION['customer_id'])) {
                echo 'window.location.href = "logout.php?confirm=user";';
            }
            ?>
        });

        document.getElementById("cancelLogout").addEventListener("click", function() {
            // Redirect back to the dashboard or login page
            <?php
            if (isset($_SESSION['admin_id'])) {
                echo 'window.location.href = "admin/home.php";';
            } elseif (isset($_SESSION['customer_id'])) {
                echo 'window.location.href = "users/dashboard.php";';
            }
            ?>
        });
    </script>

</body>

</html>