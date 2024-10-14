<?php
include '../../connection.php';

// HEADER
// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Set default values in case data is missing
$admin_username = htmlspecialchars($admin['Username'] ?? 'Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator');
// HEADER

// Fetch all reviews with customer names and product details
$sql = "SELECT r.ReviewID, c.Name AS CustomerName, p.ProductName, p.Photo AS ProductPhoto, r.Rating, r.ReviewText, r.ReviewDate
        FROM reviews r
        INNER JOIN customers c ON r.CustomerID = c.CustomerID
        INNER JOIN products p ON r.ProductID = p.ProductID";

$result = $conn->query($sql);

// Find the product with the highest rating
$sqlHighestRating = "SELECT p.ProductName, p.Photo AS ProductPhoto, AVG(r.Rating) AS AverageRating, COUNT(r.ReviewID) AS ReviewCount
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Reviews</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }

        .container-fluid {
            background: linear-gradient(to bottom, #28a745, white);
        }

        .product-image {
            max-width: 50px;
            height: auto;
            border-radius: 6px;
        }

        .product-photo img {
            max-width: 200px;
            height: auto;
            border-radius: 10px;
        }

        /* Card for Highest Rating Product */
        .card {
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .card-header {
            font-weight: bold;
            background-color: #28a745;
            color: white;
        }

        .card-footer {
            background-color: #f1f1f1;
        }

        .crown {
            color: gold;
        }

        /* Table Styling */
        table {
            width: 100%;
            margin-bottom: 20px;
            background-color: #ffffff;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        table th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .product-photo img {
                max-width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <?php include 'header.php'; ?>
        <div class="container-fluid">
            <div class="row mb-5 mt-5 py-5 px-3">
                <div class="col-12 text-center">
                    <div class="head pb-2">
                        <h3 class="fw-bold text-light" style="font-family: cursive;">
                            <i class="fa-solid fa-fire"></i> List of Reviews
                        </h3>
                    </div>
                </div>

                <div class="col-lg-9 col-md-8 col-sm-12">
                    <div class="orders-table-container">
                        <table class="table admin-dashboard table-bordered">
                            <thead>
                                <tr class="fw-bold fs-5 bg-success text-light">
                                    <th colspan="7">Reviews
                                        <span class="badge text-bg-danger"><?php echo $reviews_count; ?></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Rating</th>
                                    <th>Review Text</th>
                                    <th>Review Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $row_counter++; ?></td>
                                        <td><img src="<?php echo $row['ProductPhoto']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                                        <td><?php echo $row['CustomerName']; ?></td>
                                        <td><?php echo $row['ProductName']; ?></td>
                                        <td><?php echo $row['Rating']; ?> ⭐</td>
                                        <td><?php echo $row['ReviewText']; ?></td>
                                        <td><?php echo date("F j, Y", strtotime($row['ReviewDate'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            Product with the Highest Rating
                        </div>
                        <div class="card-body">
                            <div class="product-photo">
                                <img src="<?php echo $rowHighestRating['ProductPhoto']; ?>" alt="<?php echo $rowHighestRating['ProductName']; ?>" class="product-image">
                            </div>
                            <div class="product-details mt-3">
                                <p><strong><?php echo $rowHighestRating['ProductName']; ?></strong></p>
                                <p>Average Rating: <?php echo number_format($rowHighestRating['AverageRating'], 2); ?> <i class="fas fa-crown crown"></i></p>
                            </div>
                        </div>
                        <div class="card-footer">
                            Based on <?php echo $rowHighestRating['ReviewCount']; ?> reviews.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>