<?php
session_start();
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the customer ID and product ID from the request
    $customerID = $_SESSION['customer_id'];
    $productID = $_POST['productID'];

    // Prepare the SQL statement to delete the item from the cart
    $sql = "DELETE FROM cart WHERE CustomerID = ? AND ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customerID, $productID);

    // Execute the statement and check if successful
    if ($stmt->execute()) {
        echo "Item removed successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
