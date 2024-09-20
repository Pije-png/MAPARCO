<?php
include('../connection.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['ProductID'];
    $customerID = $_SESSION['CustomerID'];
    $rating = $_POST['Rating'];
    $reviewText = $_POST['ReviewText'];

    // Insert review into the database
    $sql = "INSERT INTO reviews (ProductID, CustomerID, Rating, ReviewText) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $productID, $customerID, $rating, $reviewText);
    if ($stmt->execute()) {
        header("Location: review_product.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
