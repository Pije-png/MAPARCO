<?php
include('../connection.php');

// Initialize variables for error messages
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if each field is set
    $orderID = isset($_POST['order_id']) ? $_POST['order_id'] : null;
    $productID = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $rating = isset($_POST['rating']) ? $_POST['rating'] : null;
    $reviewText = isset($_POST['review_text']) ? $_POST['review_text'] : null;
    $customerID = $_SESSION['customer_id'];

    // Make sure required fields are not null
    if ($orderID && $productID && $rating && $reviewText) {
        // Check if a review already exists
        $stmt = $conn->prepare("SELECT ReviewID FROM reviews WHERE OrderID = ? AND ProductID = ? AND CustomerID = ?");
        $stmt->bind_param("iii", $orderID, $productID, $customerID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing review
            $stmt = $conn->prepare("UPDATE reviews SET Rating = ?, ReviewText = ? WHERE OrderID = ? AND ProductID = ? AND CustomerID = ?");
            $stmt->bind_param("isiii", $rating, $reviewText, $orderID, $productID, $customerID);
        } else {
            // Insert new review
            $stmt = $conn->prepare("INSERT INTO reviews (OrderID, ProductID, CustomerID, Rating, ReviewText) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiss", $orderID, $productID, $customerID, $rating, $reviewText);
        }

        if ($stmt->execute()) {
            $success = 'Review saved successfully.';
        } else {
            $error = 'Error saving review. Please try again.';
        }

        $stmt->close();
    } else {
        $error = 'All fields are required.';
    }
}

// Fetch user orders from the database
$customerID = $_SESSION['customer_id'];
$sql = "SELECT o.OrderID, o.OrderDate, p.ProductID, p.ProductName, p.Photo, oi.Quantity, oi.Price, o.OrderStatus
        FROM orders o
        INNER JOIN orderitems oi ON o.OrderID = oi.OrderID
        INNER JOIN products p ON oi.ProductID = p.ProductID
        WHERE o.CustomerID = ? AND o.OrderStatus = 'Delivered'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Fetch reviews from the database
$sql = "SELECT r.ReviewID, r.OrderID, r.ProductID, r.Rating, r.ReviewText, p.ProductName, p.Photo, o.OrderDate
        FROM reviews r
        INNER JOIN products p ON r.ProductID = p.ProductID
        INNER JOIN orders o ON r.OrderID = o.OrderID
        WHERE r.CustomerID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);

// Separate reviewed and non-reviewed orders
$reviewedProductIDs = array_column($reviews, 'ProductID');
$nonReviewedOrders = array_filter($orders, function ($order) use ($reviewedProductIDs) {
    return !in_array($order['ProductID'], $reviewedProductIDs);
});

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .buy-container {
            margin: 70px auto;
        }

        .container {
            /* max-width: 800px; */
            /* background-color: #fff; */
            padding: 20px;
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
        }

        .header-container {
            background-color: #fffefb;
            border: 1px solid rgba(224, 168, 0, .4);
            border-radius: 2px;
            border-radius: 3px;
            box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            margin-bottom: 15px;
        }

        h3 {
            text-align: center;
        }

        .card-con {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            grid-gap: 20px;
        }

        .order-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .order-details p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .action button {
            margin-top: 10px;
            width: 100%;
        }

        @media (max-width: 510px) {}
    </style>
</head>

<body>
    <?php include 'navbars/navbar.php'; ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">To Review</li>
        </ol>
    </nav>

    <div class="buy-container vh-100">
        <div class="container mt-2">
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php elseif (!empty($success)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <h4 class="fw-bold text-success mb-2">Review Orders</h4>
            <div class="card header-container">
                <div class="orders-container">
                    <?php if (empty($nonReviewedOrders)) : ?>
                        <img src='users/mr3.png' alt='No cancelled orders' style='width:250px; height:auto;'>
                    <?php else : ?>
                        <?php foreach ($nonReviewedOrders as $order) : ?>
                            <div class="order-card">
                                <img src="../admin/management/<?php echo $order['Photo']; ?>" alt="<?php echo $order['ProductName']; ?>" class="product-image">
                                <div class="order-details">
                                    <h6><?php echo $order['ProductName']; ?></h6>
                                    <p>Order Date: <?php echo date("F j, Y", strtotime($order['OrderDate'])); ?></p>
                                    <p>Quantity: <?php echo $order['Quantity']; ?></p>
                                    <p>Price: ₱<?php echo $order['Price']; ?></p>
                                </div>
                                <div class="action">
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="<?php echo $order['OrderID']; ?>" data-product-id="<?php echo $order['ProductID']; ?>" data-product-name="<?php echo $order['ProductName']; ?>">Add Review</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <h4 class="fw-bold text-success mb-2">Reviewed Products</h4>
            <div class="card">
                <div class="orders-container">
                    <?php if (empty($reviews)) : ?>
                        <h5 class="text-center">No products to review.</h5>
                    <?php else : ?>
                        <?php foreach ($reviews as $review) : ?>
                            <div class="order-card">
                                <img src="../admin/management/<?php echo $review['Photo']; ?>" alt="<?php echo $review['ProductName']; ?>" class="product-image">
                                <div class="order-details">
                                    <h6><?php echo $review['ProductName']; ?></h6>
                                    <p>Order Date: <?php echo date("F j, Y", strtotime($review['OrderDate'])); ?></p>
                                    <p>Rating: <?php echo $review['Rating']; ?>⭐</p>
                                    <p>Review: <?php echo $review['ReviewText']; ?></p>
                                </div>
                                <div class="action">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="<?php echo $review['OrderID']; ?>" data-product-id="<?php echo $review['ProductID']; ?>" data-product-name="<?php echo $review['ProductName']; ?>" data-rating="<?php echo $review['Rating']; ?>" data-review-text="<?php echo $review['ReviewText']; ?>">Edit Review</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold">Write Review:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title mb-2 text-success fw-bold" id="reviewModalLabel"><span id="modalProductName"></span></h5>
                    <form id="reviewForm" method="post">
                        <input type="hidden" name="order_id" id="modalOrderId">
                        <input type="hidden" name="product_id" id="modalProductId">
                        <div class="mb-3">
                            <label for="rating" class="form-label fw-bold">Rating:</label>
                            <select name="rating" id="rating" class="form-select">
                                <option value="1">1⭐Poor</option>
                                <option value="2">2⭐⭐- Fair</option>
                                <option value="3">3⭐⭐⭐- Good</option>
                                <option value="4">4⭐⭐⭐⭐- Ve ry Good</option>
                                <option value="5">5⭐⭐⭐⭐⭐- Excellent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="review_text" class="form-label fw-bold">Review:</label>
                            <textarea name="review_text" id="review_text" rows="4" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var reviewModal = document.getElementById('reviewModal');
        reviewModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var orderId = button.getAttribute('data-order-id');
            var productId = button.getAttribute('data-product-id');
            var productName = button.getAttribute('data-product-name');
            var rating = button.getAttribute('data-rating') || '';
            var reviewText = button.getAttribute('data-review-text') || '';

            var modalOrderIdInput = document.getElementById('modalOrderId');
            var modalProductIdInput = document.getElementById('modalProductId');
            var modalProductNameSpan = document.getElementById('modalProductName');
            var modalRatingSelect = document.getElementById('rating');
            var modalReviewText = document.getElementById('review_text');

            modalOrderIdInput.value = orderId;
            modalProductIdInput.value = productId;
            modalProductNameSpan.textContent = productName;
            modalRatingSelect.value = rating;
            modalReviewText.value = reviewText;
        });
    </script>
</body>

</html>