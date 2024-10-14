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
    <title>Reviews</title>
    <link rel="icon" href="img/MAPARCO.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        /* General Styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Container */
        .container-fluid {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Card Styles */
        .card {
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-bottom: none;
            background-color: #28a745;
            color: #ffffff;
        }

        .card-body h5 {
            font-size: 1.1rem;
            color: #333;
        }

        .card-body p {
            font-size: 0.9rem;
            color: #555;
        }

        .crown {
            color: gold;
            margin-left: 5px;
        }

        /* Badge */
        .badge {
            font-size: 1rem;
        }

        /* Responsive Images */
        .product-photo img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* Responsive Typography */
        @media (max-width: 768px) {
            .card-body h5 {
                font-size: 1rem;
            }

            .card-body p {
                font-size: 0.85rem;
            }
        }

        /* Sticky Sidebar */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
    </style>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <?php include 'header.php'; ?>
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h3 class="fw-bold text-dark" style="font-family: cursive;">
                        <i class="fa-solid fa-fire"></i> List of Reviews
                    </h3>
                    <span class="badge bg-danger"><?php echo $reviews_count; ?></span>
                </div>
            </div>

            <!-- Reviews and Top Rated Product -->
            <div class="row">
                <!-- Reviews Section -->
                <div class="col-lg-9">
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="row g-0">
                                        <div class="col-4 product-photo">
                                            <img src="<?php echo $row['ProductPhoto']; ?>" alt="<?php echo $row['ProductName']; ?>" class="img-fluid rounded-start" loading="lazy">
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $row['ProductName']; ?></h5>
                                                <p class="card-text"><strong>Customer:</strong> <?php echo $row['CustomerName']; ?></p>
                                                <p class="card-text">
                                                    <strong>Rating:</strong>
                                                    <?php echo $row['Rating']; ?>⭐
                                                </p>
                                                <p class="card-text"><?php echo $row['ReviewText']; ?></p>
                                                <p class="card-text"><small class="text-muted"><?php echo date("F j, Y", strtotime($row['ReviewDate'])); ?></small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Top Rated Product Section -->
                <div class="col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            Top Rated Product
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo $rowHighestRating['ProductPhoto']; ?>" alt="<?php echo $rowHighestRating['ProductName']; ?>" class="img-fluid rounded mb-3" style="max-width: 150px;" loading="lazy">
                            <h6><?php echo $rowHighestRating['ProductName']; ?></h6>
                            <p><strong>Average Rating:</strong> <?php echo number_format($rowHighestRating['AverageRating'], 2); ?> <i class="fas fa-crown crown"></i></p>
                            <p><strong>Total Reviews:</strong> <?php echo $rowHighestRating['TotalReviews']; ?></p>
                            <a href="product-details.php?id=<?php echo $rowHighestRating['ProductID']; ?>" class="btn btn-primary btn-sm mt-2">View Product</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>
