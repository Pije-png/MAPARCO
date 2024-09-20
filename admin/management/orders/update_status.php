<?php
include '../../../connection.php';

// Set the content type to application/json
header('Content-Type: application/json');

// Check if the request contains order_id
if (isset($_POST['order_id'])) {
    $orderID = intval($_POST['order_id']);

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE orders SET OrderStatus = 'Delivered' WHERE OrderID = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind the orderID parameter
    $stmt->bind_param("i", $orderID);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order status updated to Delivered']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request: order_id missing']);
}
?>
