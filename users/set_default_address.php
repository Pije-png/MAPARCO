<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['set_default'])) {
    $customer_id = $_SESSION['customer_id'];
    $address_id = $_POST['address_id'];

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

    // Set the selected address as default
    $update_default_query = "UPDATE addresses SET IsDefault = 0 WHERE CustomerID = '$customer_id'";
    $conn->query($update_default_query);
    $update_default_query = "UPDATE addresses SET IsDefault = 1 WHERE AddressID = '$address_id' AND CustomerID = '$customer_id'";
    $conn->query($update_default_query);

    // Close the database connection
    $conn->close();

    // Redirect back to the address page or any other page as needed
    header("Location: address.php");
    exit;
}
