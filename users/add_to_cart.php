<?php
session_start(); // Start the session
include('../connection.php');

if (!isset($_SESSION['customer_id'])) {
    echo "Please log in to add items to your cart.";
    exit;
}

$customerID = $_SESSION['customer_id'];

if (isset($_POST['ProductID']) && isset($_POST['Quantity'])) {
    $productID = $_POST['ProductID'];
    $quantity = $_POST['Quantity'];

    // Check if product is already in cart
    $sql = "SELECT Quantity FROM cart WHERE CustomerID = ? AND ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customerID, $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if product is already in cart
        $row = $result->fetch_assoc();
        $newQuantity = $row['Quantity'] + $quantity;
        $sql = "UPDATE cart SET Quantity = ? WHERE CustomerID = ? AND ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $newQuantity, $customerID, $productID);
    } else {
        // Insert new item into cart
        $sql = "INSERT INTO cart (CustomerID, ProductID, Quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $customerID, $productID, $quantity);
    }

    if ($stmt->execute()) {
        echo "Product added to cart successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
} else {
    echo "ProductID or Quantity not provided";
}

$conn->close();
?>
