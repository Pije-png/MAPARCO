<?php
include '../../connection.php';

// Fetch all reviews with customer names and product details
$sql = "SELECT r.ReviewID, c.Name AS CustomerName, p.ProductName, p.Photo AS ProductPhoto, r.Rating, r.ReviewText, r.ReviewDate
        FROM reviews r
        INNER JOIN customers c ON r.CustomerID = c.CustomerID
        INNER JOIN products p ON r.ProductID = p.ProductID";

$result = $conn->query($sql);

// Find the product with the highest rating
$sqlHighestRating = "SELECT p.ProductName, p.Photo AS ProductPhoto, AVG(r.Rating) AS AverageRating
                     FROM products p
                     INNER JOIN reviews r ON p.ProductID = r.ProductID
                     GROUP BY p.ProductID
                     ORDER BY AverageRating DESC
                     LIMIT 1";
$resultHighestRating = $conn->query($sqlHighestRating);
$rowHighestRating = $resultHighestRating->fetch_assoc();
// Get the number of products
$reviews_count = $result->num_rows;
// Initialize row counter
$row_counter = 1;
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Reviews</title>
</head>
<style>
    .product-image {
        max-width: 50px;
        height: auto;
        border-radius: 6px;
    }

    .product-photo img {
        max-width: 100px;
        height: auto;
    }

    .highest-rating {
        text-align: center;
    }

    .product-info {
        display: inline-block;
        background-color: #15BE2F;
        /* Green */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .product-details p {
        font-size: 18px;
        margin: 8px 0;
        color: #fff;
    }

    .crown {
        color: gold;
    }
</style>
<style>
    .admin-dashboard {
        width: 100%;
        border-collapse: collapse;
        /* margin-bottom: 0; */
    }

    table {
        border-collapse: collapse;
    }

    table tr,
    table th,
    table td {
        font-size: 12px;
        border: 1px solid #999;
    }
        table tr,
    table th{
        padding: 5px;
    }

    thead {
        background-color: #98FB98;
    }

    .column {
        margin-bottom: 0;
    }

    .editbtn {
        width: 100%;
    }

    .order-status-processing {
        color: orange;
    }

    .order-status-processing {
        color: blue;
    }

    .order-status-shipped {
        color: #15BE2F;
    }

    .order-status-delivered {
        color: #888;
    }

    .payment-status-processing {
        color: orange;
    }

    .payment-status-paid {
        color: #888;
    }

    input[type="checkbox"] {
        transform: scale(1.5);
    }
</style>
</head>

<body class="bg bg-light">

    <?php include 'sidebar.php'; ?>

     

    <section class="home">
        <div class="order-container">
            <div class="container-fluid">
                <div class="highest-rating pt-3">
                    <!-- <h2 class="mt-0">Product with the Highest Rating</h2> -->
                    <div class="product-info">
                        <div class="product-photo">
                            <img src="<?php echo $rowHighestRating['ProductPhoto']; ?>" alt="<?php echo $rowHighestRating['ProductName']; ?>" class="product-image">
                        </div>
                        <div class="product-details">
                            <p><strong>Product Name:</strong> <?php echo $rowHighestRating['ProductName']; ?></p>
                            <p><strong>Average Rating:</strong> <?php echo number_format($rowHighestRating['AverageRating'], 2); ?>
                                <i class="fas fa-crown crown"></i>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="head pt-3">
                    <h4 class="text-center">Reviews</h4>
                </div>
                <div class="column">
                    <div class="status-messages">
                        <?php if (isset($global_update_message)) {
                            echo "<div class='status-message'>" . htmlspecialchars($global_update_message) . "</div>";
                        } ?>
                    </div>
                </div>
                <div class="orders-table-container">
                    <div class="header-container">
                        <table class="admin-dashboard">
                            <thead>
                                <tr class="fw-bold fs-5 bg bg-success text-light">
                                    <th colspan="7">Product with the Highest Rating
                                                   <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $reviews_count; ?></span>
                                    </th>

                                </tr>
                                <tr class="text-center">
                                    <th style="width:2%"></th>
                                    <th>Customer Name</th>
                                    <th>Product Name</th>
                                    <th>Product Photo</th>
                                    <th>Rating</th>
                                    <th>Review Text</th>
                                    <th>Review Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                    <td><?php echo $row_counter++; ?></td> <!-- Row counter increment -->
                                        <td><?php echo $row['CustomerName']; ?></td>
                                        <td><?php echo $row['ProductName']; ?></td>
                                        <td><img src="<?php echo $row['ProductPhoto']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                                        <td><?php echo $row['Rating']; ?>⭐</td>
                                        <td><?php echo $row['ReviewText']; ?></td>
                                        <td><?php echo date("F j, Y", strtotime($row['ReviewDate'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </section>

</body>

</html>