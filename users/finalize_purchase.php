<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// session_start();

include('../connection.php');

// Retrieve data from the session or POST request
$customerID = $_SESSION['customer_id']; // Assuming this is set after user login
$productID = $_POST['ProductID'];
$quantity = $_POST['Quantity'];
$shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : '';

// Start transaction
$conn->begin_transaction();

try {
    // Fetch default address for the customer
    $sql_default_address = "SELECT * FROM addresses WHERE CustomerID = ? AND IsDefault = 1";
    $stmt_default_address = $conn->prepare($sql_default_address);
    $stmt_default_address->bind_param("i", $customerID);
    $stmt_default_address->execute();
    $result_default_address = $stmt_default_address->get_result();

    if ($result_default_address->num_rows === 0) {
        throw new Exception("Default address not found.");
    }

    $defaultAddress = $result_default_address->fetch_assoc();
    $shippingAddress = $defaultAddress['Description'] . ', ' . $defaultAddress['HouseNo'] . ', ' . $defaultAddress['Street'] . ', ' . $defaultAddress['Barangay'] . ', ' . $defaultAddress['City'] . ', ' . $defaultAddress['Province'] . ', ' . $defaultAddress['ZipCode'];

    // Fetch product details based on ProductID
    $sql_product = "SELECT ProductName, Price FROM products WHERE ProductID = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $productID);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();

    if ($result_product->num_rows === 0) {
        throw new Exception("Product not found.");
    }

    $productInfo = $result_product->fetch_assoc();

    // Calculate subtotal
    $subtotal = $productInfo['Price'] * $quantity;

    // Adjust total amount based on shipping method
    $shippingCost = 0;
    if ($shippingMethod === 'door_to_door') {
        $shippingCost = 58;
    }

    $totalAmount = $subtotal + $shippingCost;

    // Insert order into orders table
    $orderDate = date("Y-m-d");
    $orderStatus = "Pending";
    $paymentStatus = "Pending";

    $sql_order = "INSERT INTO orders (CustomerID, OrderDate, TotalAmount, OrderStatus, PaymentStatus, ShippingAddress, AddressID)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("isdsssi", $customerID, $orderDate, $totalAmount, $orderStatus, $paymentStatus, $shippingAddress, $defaultAddress['AddressID']);
    $stmt_order->execute();

    // Get the last inserted order ID
    $orderID = $stmt_order->insert_id;

    // Insert order item into orderitems table
    $sql_order_item = "INSERT INTO orderitems (OrderID, ProductID, Quantity, Price, Subtotal, ProductName)
                       VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order_item = $conn->prepare($sql_order_item);
    $stmt_order_item->bind_param("iiidds", $orderID, $productID, $quantity, $productInfo['Price'], $subtotal, $productInfo['ProductName']);
    $stmt_order_item->execute();

    // Update product quantity in the Products table
    $sqlSelect = "SELECT QuantityAvailable FROM products WHERE ProductID = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("i", $productID);
    $stmtSelect->execute();
    $resultSelect = $stmtSelect->get_result();

    if ($resultSelect->num_rows > 0) {
        $row = $resultSelect->fetch_assoc();
        $currentQuantity = $row['QuantityAvailable'];

        if ($currentQuantity >= $quantity) {
            $newQuantity = $currentQuantity - $quantity;
            $sqlUpdate = "UPDATE products SET QuantityAvailable = ? WHERE ProductID = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $newQuantity, $productID);
            $stmtUpdate->execute();
        } else {
            throw new Exception("Insufficient quantity available.");
        }
    } else {
        throw new Exception("Product not found.");
    }

    // Commit transaction
    $conn->commit();

    // Close statements and connection
    $stmt_default_address->close();
    $stmt_product->close();
    $stmt_order->close();
    $stmt_order_item->close();
    $stmtSelect->close();
    $stmtUpdate->close();
    $conn->close();

    // Redirect to Purchase.php after ordering
    header("Location: purchase.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();

    // Close statements and connection
    if (isset($stmt_default_address)) $stmt_default_address->close();
    if (isset($stmt_product)) $stmt_product->close();
    if (isset($stmt_order)) $stmt_order->close();
    if (isset($stmt_order_item)) $stmt_order_item->close();
    if (isset($stmtSelect)) $stmtSelect->close();
    if (isset($stmtUpdate)) $stmtUpdate->close();
    $conn->close();

    echo "Error: " . $e->getMessage();
}
?>
