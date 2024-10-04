<?php include '../connection.php';

function redirectToLogin()
{
    header("Location: ../login.php");
    exit;
}

// Perform the logout operation when a request is made to log out
if (isset($_POST['confirmLogout'])) {
    if (isset($_SESSION['admin_id'])) {
        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session for the admin
        session_destroy();
        // Redirect to login page
        redirectToLogin();
    } elseif (isset($_SESSION['customer_id'])) {
        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session for the customer
        session_destroy();
        // Redirect to login page
        redirectToLogin();
    }
}

// Queries for charts and top-selling products (unchanged)
$topSellingProductsQuery = "
SELECT
p.ProductName, p.Price, p.Photo,
COALESCE(AVG(r.Rating), 0) AS AverageRating,
SUM(o.Quantity) AS Sales
FROM orderitems o
JOIN orders ord ON o.OrderID = ord.OrderID
JOIN products p ON o.ProductID = p.ProductID
LEFT JOIN reviews r ON r.ProductID = p.ProductID
WHERE ord.PaymentStatus = 'Paid'
GROUP BY o.ProductID
ORDER BY Sales DESC
LIMIT 5
";

$topSellingProductsResult = $conn->query($topSellingProductsQuery);

// Query for all top-selling products
$allTopSellingProductsQuery = "
SELECT
p.ProductName, p.Price, p.Photo,
COALESCE(AVG(r.Rating), 0) AS AverageRating,
SUM(o.Quantity) AS Sales
FROM orderitems o
JOIN orders ord ON o.OrderID = ord.OrderID
JOIN products p ON o.ProductID = p.ProductID
LEFT JOIN reviews r ON r.ProductID = p.ProductID
WHERE ord.PaymentStatus = 'Paid'
GROUP BY o.ProductID
ORDER BY Sales DESC
";
$allTopSellingProductsResult = $conn->query($allTopSellingProductsQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <link rel="stylesheet" href="css/dashboarduser.css"> -->
    <style>
        /* <style> */

        /* Custom Styles */
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            min-height: 100vh;
            background-color: rgb(232, 247, 232);
            padding: 20px;
        }

        .product {
            margin-top: 30px;
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.2);
            padding: 5px;
            border-radius: 10px;
        }

        .product-image {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            /* border-bottom: 1px solid #c1c1c1; */
            margin-bottom: 15px;
        }

        .col .card-body {
            text-align: left;
            padding: 10px;
            font-size: 14px;
            max-width: 150px;
        }

        .col .card-body p {
            font-size: 13px;
        }

        .product-price {
            color: red;
        }

        /* Grid and responsive styles */
        .col .card {
            padding: 10px;
            transition: transform 0.3s ease;
            text-decoration: none;
            min-height: 240px;
            max-height: 240px;

        }

        .card:hover {
            border: 1px solid red;
            text-decoration: none;
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }

        .carousel-item img {
            max-width: 100%;
            height: 280px;
            /* margin-top: 40px; */
        }

        .star-rating i {
            color: #f39c12;
        }

        /* Media query for Android size */
        @media (max-width: 510px) {
            .modal-content {
                top: 10%;
                width: 64%;
                /* 20% smaller width for Android devices */
                max-width: 320px;
                /* Adjust maximum width as needed */
            }

            .modal img {
                width: 100%;
            }

            .text a {
                margin-top: 20px;
                width: 50%;
                padding: 10px;
            }

            .login_continue button {
                font-size: 13px;
                /* Set the font size */
                width: 100%;
                /* Make the button full-width */
                background-color: #dc3545;
                /* Bootstrap 'danger' color */
                color: #fff;
                /* White text */
                border: none;
                /* Remove border */
                padding: 10px;
                /* Padding for the button */
                border-radius: 5px;
                /* Rounded corners */
                cursor: pointer;
                /* Pointer cursor on hover */
            }

            .login_continue button:hover {
                background-color: #c82333;
                /* Darker shade on hover */
            }

        }

        @media (max-width: 510px) {

            /* Adjust the number of columns to 2 for smaller screens */
            .row.row-cols-md-5 {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Adjust font size for smaller screens */
            .card-body p {
                font-size: 12px;
                line-height: 12px;
            }

            .card {
                padding: 0px;
                min-height: 170px;
                max-height: auto;
                width: 100%;
            }

            .product-image {
                max-height: 100px;
                margin-bottom: 5px;
            }

            .star,
            .text-muted {
                font-size: 12px;
            }

            .div h3 {
                font-size: 20px;
            }

            .carousel-item img {
                max-width: 100%;
                height: auto;
                /* margin-top: 40px; */
            }
        }


        /* Small devices (landscape phones, 576px and up) */
        @media (max-width: 576px) {
            .topselling {
                display: none;
            }
        }

        /* Medium devices (tablets, 768px and up) */
        @media (min-width: 768px) {
            .topselling {
                display: block;
            }
        }

        h6.card-title {
            font-size: 1rem;
            letter-spacing: 1px;
            color: #4A4A4A;
            text-transform: uppercase;
            /* border-bottom: 2px solid green; */
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .div h4 {
            letter-spacing: 1px;
            font-weight: bold;
        }

        .topselling {
            /* background-color: #15BE2F; */
            background-color: #f1f1f1;
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.2);
        }

        .topselling p {
            font-size: 12px;
        }

        /* For WebKit browsers (Chrome, Safari, Edge) */
        .topselling.col-md-4::-webkit-scrollbar {
            width: 5px;
        }

        .topselling.col-md-4::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
        }

        .topselling.col-md-4::-webkit-scrollbar-thumb {
            background-color: #c1c1c1;
            border-radius: 10px;
        }

        .topselling.col-md-4::-webkit-scrollbar-thumb:hover {
            background-color: #a1a1a1;
        }

        .topselling.col-md-4 {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        /* Rank badge similar to the uploaded image */
        .rank-badge {
            position: absolute;
            top: 0px;
            left: 0px;
            background: linear-gradient(to bottom, #ff7f00, #ff4500);
            /* Gradient color similar to the image */
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            width: 30px;
            height: 30px;
            clip-path: polygon(50% 100%, 100% 70%, 100% 0%, 0% 0%, 0% 70%);
        }

        /* Highlight first product */
        .highlight-product {
            background-color: #fff3cd;
            /* Light yellow background for the top product */
        }
    </style>
</head>

<body>
    <?php include 'navbar_dashboard.php'; ?>
    <div class="">
        <div class="container">
            <div class="row pt-5">
                <!-- Carousel Section -->
                <div class="col-md-8">
                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="../img/bgs/slide1.png" class="d-block w-100" alt="Slide 1">
                            </div>
                            <div class="carousel-item">
                                <img src="../img/bgs/slide2.png" class="d-block w-100" alt="Slide 2">
                            </div>
                            <div class="carousel-item">
                                <img src="../img/bgs/slide3.png" class="d-block w-100" alt="Slide 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <div class="topselling bg-light rounded col-md-4 p-2 shadow-sm" style="height: 280px; overflow: auto; border-radius: 8px;">
                    <div>
                        <h6 class="card-title text-success fw-bold text-center pt-2">Top Products</h6>
                    </div>
                    <?php
                    $counter = 0; // Initialize a counter

                    if ($topSellingProductsResult->num_rows > 0) {
                        while ($row = $topSellingProductsResult->fetch_assoc()) {
                            // Increment counter
                            $counter++;

                            // Display only the first 3 products
                            if ($counter > 3) {
                                break; // Stop loop after 3 products
                            }

                            // CSS class to highlight the first product
                            $highlightClass = ($counter == 1) ? 'highlight-product' : '';
                    ?>
                            <div class="mx-2">
                                <div class="card mb-2 rounded-0 border shadow-sm <?php echo $highlightClass; ?>">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-md-3 position-relative">
                                            <!-- Rank badge -->
                                            <div class="rank-badge">
                                                <span><?php echo $counter; ?></span>
                                            </div>
                                            <img src="../admin/management/<?php echo $row['Photo']; ?>" class="img-fluid rounded-start" alt="<?php echo $row['ProductName']; ?>" style="height: 98px; width: auto;">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body p-2">
                                                <!-- Display rank number -->
                                                <p class="card-title mb-1 font-weight-bold">
                                                    <?php echo $row['ProductName']; ?>
                                                    <?php if ($counter == 1): ?>
                                                        <i class="bi bi-crown text-warning"></i> <!-- Crown icon for the first product -->
                                                    <?php endif; ?>
                                                </p>
                                                <p class="card-text text-success mb-1">&#8369;<?php echo number_format($row['Price'], 2); ?></p>
                                                <div class="star-rating d-flex align-items-center">
                                                    <?php
                                                    $avg_rating = round($row['AverageRating'], 1);
                                                    $full_stars = intval($avg_rating);
                                                    $empty_stars = 5 - $full_stars;
                                                    for ($i = 0; $i < $full_stars; $i++) {
                                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                                    }
                                                    for ($i = 0; $i < $empty_stars; $i++) {
                                                        echo '<i class="bi bi-star text-muted"></i>';
                                                    }
                                                    ?>
                                                    <span class="text-muted ml-2 small">(<?php echo $avg_rating; ?>/5)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p class='text-muted'>No top-selling products found</p>";
                    }
                    ?>
                </div>
            </div>


            <div class="product rounded-0 mt-3 bg bg-light p-3">
                <div class="div">
                    <!-- <h4 class="text-center text-success">PRODUCTS</h4> -->
                </div>
                <div class="row row-cols-md-5 row-cols-sm-2 row-cols-2 g-2 mt-2 px-md-5 px-sm-2">
                    <?php
                    // Fetch and shuffle product data from the database
                    $sql = "SELECT p.ProductID, p.Photo, p.ProductName, p.Description, p.Price, AVG(r.Rating) AS AvgRating
                            FROM products p
                            LEFT JOIN reviews r ON p.ProductID = r.ProductID
                            GROUP BY p.ProductID
                            ORDER BY RAND()";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // Calculate star rating
                            $avg_rating = round($row["AvgRating"], 1);
                            $full_stars = intval($avg_rating);
                            $empty_stars = 5 - $full_stars;
                    ?>
                            <div class="col">
                                <a href='view_product.php?ProductID=<?php echo $row["ProductID"]; ?>' class='card rounded-1 grid-item'>
                                    <img src='../admin/management/<?php echo $row["Photo"]; ?>' alt='Product Image' class='card-img-top product-image'>
                                    <div class='card-body product-details p-0'>
                                        <p class='card-title product-name'><?php echo $row["ProductName"]; ?></p>
                                        <div class="star-rating">
                                            <?php
                                            // Display full stars
                                            for ($i = 0; $i < $full_stars; $i++) {
                                                echo '<i class="bi bi-star-fill text-warning star"></i>';
                                            }
                                            // Display empty stars
                                            for ($i = 0; $i < $empty_stars; $i++) {
                                                echo '<i class="bi bi-star text-warning star"></i>';
                                            }
                                            ?>
                                            <span class="text-muted">(<?php echo $avg_rating; ?>/5)</span>
                                        </div>
                                        <p class='card-text product-price'>&#8369;<?php echo $row["Price"]; ?></p>
                                    </div>
                                </a>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p class='col'>No products found</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- <footer class="mt-1" style="background-color: limegreen; color: #fff; padding: 40px 0; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between;">

            <div style="flex: 1 1 200px; margin: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 18px;">About Us</h4>
                <p style="line-height: 1.6; font-size:13px">
                    At MAPARCO, we are dedicated to enhancing your online shopping experience. Our mission is to provide exceptional service and innovative solutions, ensuring you find exactly what you're looking for with ease and confidence. We are committed to quality, convenience, and customer satisfaction, striving to make every purchase a seamless and enjoyable journey.
                </p>
            </div>

            <div style="flex: 1 1 200px; margin: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 18px;">Contact Us</h4>
                <p style="line-height: 1.6; font-size:13px">
                    Address: Sitio Dos, Mahabang Parang, Naujan, Oriental Mindoro, Philippines<br>
                    Phone: (+63)916 7466 766<br>
                    Email: tapaspatrickjames@gmail.com
                </p>
            </div>

            <div style="flex: 1 1 200px; margin: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 18px;">Follow Us</h4>
                <div style=" font-size:13px">
                    <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Facebook</a>
                    <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Twitter</a>
                    <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Instagram</a>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid #444; margin-top: 40px; padding-top: 20px;">
            <p>&copy; 2024 MAPARCO. All rights reserved.</p>
        </div>
    </footer> -->

    <!-- JavaScript for auto-sliding carousel every 2 seconds -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myCarousel = document.getElementById('carouselExampleIndicators');
            var carousel = new bootstrap.Carousel(myCarousel, {
                interval: 2500,
                wrap: true
            });
        });
    </script>
</body>

</html>