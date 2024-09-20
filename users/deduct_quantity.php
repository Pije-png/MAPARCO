<?php
include('../connection.php');

// Check if ProductID and Quantity are provided in the POST request
if (isset($_POST['ProductID'], $_POST['Quantity'])) {
    $productID = $_POST['ProductID'];
    $quantity = $_POST['Quantity'];

    // Fetch current quantity of the product from the database
    $sqlSelect = "SELECT QuantityAvailable FROM products WHERE ProductID = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("i", $productID);
    $stmtSelect->execute();
    $resultSelect = $stmtSelect->get_result();

    if ($resultSelect->num_rows > 0) {
        $row = $resultSelect->fetch_assoc();
        $currentQuantity = $row['QuantityAvailable'];

        // Check if the current quantity is sufficient for the purchase
        if ($currentQuantity >= $quantity) {
            // Calculate the new quantity after deduction
            $newQuantity = $currentQuantity - $quantity;

            // Update the database with the new quantity
            $sqlUpdate = "UPDATE products SET QuantityAvailable = ? WHERE ProductID = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $newQuantity, $productID);
            if ($stmtUpdate->execute()) {
                // Quantity deduction successful
                echo "Quantity deducted successfully";
            } else {
                // Error updating quantity
                echo "Error updating quantity";
            }
        } else {
            // Insufficient quantity
            echo "Insufficient quantity available";
        }
    } else {
        // Product not found
        echo "Product not found";
    }

    // Close the prepared statements
    $stmtSelect->close();
    $stmtUpdate->close();
} else {
    // ProductID or Quantity not provided
    echo "ProductID or Quantity not provided";
}

// Close the database connection
$conn->close();
?>
