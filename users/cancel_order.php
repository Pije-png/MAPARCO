<?php
include('../connection.php');

if (isset($_GET['order_id'])) {
    $orderID = $_GET['order_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the product ID and quantity for the order
        $sql = "SELECT oi.ProductID, oi.Quantity
                FROM orderitems oi
                WHERE oi.OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $orderItems = $result->fetch_all(MYSQLI_ASSOC);

            // Update the order status to 'Cancelled' in the database
            $sql = "UPDATE orders SET OrderStatus = 'Cancelled' WHERE OrderID = ? AND OrderStatus = 'Pending'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $orderID);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Loop through each order item to update the product quantity
                foreach ($orderItems as $item) {
                    $productID = $item['ProductID'];
                    $quantity = $item['Quantity'];

                    // Update the product quantity in the products table
                    $sql = "UPDATE products SET QuantityAvailable = QuantityAvailable + ? WHERE ProductID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $quantity, $productID);
                    $stmt->execute();
                }

                // Commit the transaction
                $conn->commit();

                // Redirect to the purchases page after successful cancellation
                header("Location: purchase.php");
                exit;
            } else {
                throw new Exception("Failed to cancel the order.");
            }
        } else {
            throw new Exception("Order items not found.");
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>