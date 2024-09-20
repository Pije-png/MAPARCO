<?php
include('../connection.php');

// Check if OrderID is provided in the URL
if(isset($_GET['OrderID'])) {
    $orderID = $_GET['OrderID'];

    // Fetch order details from the database
    $sql = "SELECT o.OrderDate, p.ProductName, o.TotalAmount, o.OrderStatus
            FROM orders o
            INNER JOIN orderitems oi ON o.OrderID = oi.OrderID
            INNER JOIN products p ON oi.ProductID = p.ProductID
            WHERE o.OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // Close the prepared statement
    $stmt->close();
} else {
    // Redirect to a page indicating that OrderID is not provided
    header("Location: error.php");
    exit();
}

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Process form data (insert review into the database)
    // Add your code here to process the review submission

    // Redirect to a page indicating successful review submission
    header("Location: review_submitted.php");
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Make Review</h2>
        <div class="row">
            <div class="col">
                <p>Order Date: <?php echo $order['OrderDate']; ?></p>
                <p>Product Name: <?php echo $order['ProductName']; ?></p>
                <p>Total Amount: <?php echo $order['TotalAmount']; ?></p>
                <p>Order Status: <?php echo $order['OrderStatus']; ?></p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <form method="POST">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating:</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
