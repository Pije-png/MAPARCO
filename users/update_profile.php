<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Get user input from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $customer_id = $_SESSION['customer_id'];

    // Prepare update statement
    $sql = "UPDATE customers SET Name = '$name', Email = '$email' WHERE CustomerID = '$customer_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the profile page after successful update
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
