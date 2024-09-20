<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connection.php');

$productID = $_POST['ProductID'];
$quantity = $_POST['Quantity'];

$sql_product = "SELECT * FROM products WHERE ProductID = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $productID);
$stmt_product->execute();
$result_product = $stmt_product->get_result();
$productInfo = $result_product->fetch_assoc();

$customerID = $_SESSION['customer_id'];

$sql_address = "SELECT * FROM addresses WHERE CustomerID = ? AND IsDefault = 1 ORDER BY AddedAt DESC LIMIT 1";
$stmt_address = $conn->prepare($sql_address);
$stmt_address->bind_param("i", $customerID);
$stmt_address->execute();
$result_address = $stmt_address->get_result();
$defaultAddress = $result_address->fetch_assoc();

$stmt_product->close();
$stmt_address->close();
$conn->close();

$response = array(
    'productInfo' => $productInfo,
    'defaultAddress' => $defaultAddress,
    'quantity' => $quantity
);

echo json_encode($response);
?>
