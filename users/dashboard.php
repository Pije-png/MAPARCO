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
    <style>
        .container-md {
            padding-top: 15px;
        }
        
        .product {
            border-radius: 10px;
        }

        .product-image {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .col .card-body {
            text-align: left;
            padding: 10px;
            font-size: 13px;
            max-width: 150px;
        }

        .product-price {
            color: red;
            position: absolute;
            bottom: 2px;
        }

        .col .card {
            padding: 10px;
            transition: transform 0.3s ease;
            text-decoration: none;
            min-height: 230px;
            max-height: auto;
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
                max-width: 320px;
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
                width: 100%;
                background-color: #dc3545;
                color: #fff;
                border: none;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
            }

            .login_continue button:hover {
                background-color: #c82333;
            }

        }

        @media (max-width: 510px) {
            .row.row-cols-md-5 {
                grid-template-columns: repeat(2, 1fr);
            }

            .card-body p {
                font-size: 12px;
                line-height: 12px;
            }

            .col .card {
                padding: 10px;
                min-height: 182px;
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
            }

            .top-products-container .top-product-item {
                width: 130px;
            }

            .top-product-item a img {
                height: 100px;
                object-fit: contain;
            }
        }

        h6 {
            text-transform: uppercase;
            font-weight: bold;
        }
    </style>
    <style>
        .product-label {
            border-radius: 10px;
            border-bottom: 5px solid #15BE2F;
            /* box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.2); */
        }

        .product,
        .product-label,
        .top-products-container {
            background-color: #ffffff;
        }

        .product p,
        .top-products-container p {
            font-size: 12px;
        }

        .top-product-label {
            border-bottom: 1px solid #c6c6c6;
        }

        .top-product-item {
            width: 180px;
        }

        .top-product-item img {
            height: 120px;
            object-fit: contain;
        }

        .top-products-container {
            /* box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.2); */
            padding: 5px;
            border-radius: 10px;
        }

        .top-products {
            white-space: nowrap;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        .top-products::-webkit-scrollbar {
            height: 8px;
        }

        .top-products::-webkit-scrollbar-thumb {
            background-color: transparent;
        }

        .top-products::-webkit-scrollbar-track {
            background-color: transparent;
        }

        .top-product-item {
            flex: 0 0 auto;
        }

        .rankbadge {
            position: absolute;
            top: 0px;
            left: 15px;
            transform: translateX(-50%);
            background: linear-gradient(to bottom, #ff7f00, #ff4500);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            width: 30px;
            height: 30px;
            clip-path: polygon(50% 100%, 100% 70%, 100% 0%, 0% 0%, 0% 70%);
        }
        /* For small phones (iPhone SE, older Android phones) */
@media (max-width: 320px) {
    .product {
        background-color: transparent;
    }
}

/* For most smartphones (iPhone 6/7/8, X, 11, 12, 13, 14, Android phones) */
@media (max-width: 414px) {
    .product {
        background-color: transparent;
    }
}

/* For larger phones and smaller tablets (Android phones, smaller tablets, iPad Mini) */
@media (max-width: 600px) {
    .product {
        background-color: transparent;
    }
}

/* For medium tablets (iPad Mini, standard iPads, Android tablets) */
@media (max-width: 768px) {
    .product {
        background-color: transparent;
    }
}

/* For larger tablets (iPad Air, iPad Pro, Android large tablets) */
@media (max-width: 834px) {
    .product {
        background-color: transparent;
    }
}
    </style>
</head>

<body>
    <?php include 'navbar_dashboard.php'; ?>
    <div class="container-md">
        <div class="row pt-5">
            <!-- Carousel Section -->
            <div class="col-md-12">
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
        </div>

        <div class="top-products-container rounded-0 mt-3 p-0">
            <div class="top-product-label d-flex justify-content-between p-3">
                <h6 class="text-success fw-semibold">Top Products</h6>
                <!-- <a href="all_top_products.php" class="text-success">See All &gt;</a> -->
            </div>

            <!-- Horizontal Scroll for Top Products -->
            <div class="top-products d-flex overflow-auto p-3">
                <?php
                // Fetch the top-rated products from the database (limit to 6)
                $sql_top = "SELECT p.ProductID, p.Photo, p.ProductName, p.Price, SUM(o.Quantity) AS TotalSales, AVG(r.Rating) AS AvgRating
                    FROM products p
                    LEFT JOIN orderitems o ON p.ProductID = o.ProductID
                    LEFT JOIN reviews r ON p.ProductID = r.ProductID
                    GROUP BY p.ProductID
                    ORDER BY TotalSales DESC
                    LIMIT 6";

                $result_top = $conn->query($sql_top);

                if ($result_top->num_rows > 0) {
                    while ($row_top = $result_top->fetch_assoc()) {
                        // Calculate star rating
                        $avg_rating_top = round($row_top["AvgRating"], 1);
                        $full_stars_top = intval($avg_rating_top);
                        $empty_stars_top = 5 - $full_stars_top;
                        $monthly_sales = number_format($row_top["TotalSales"]) . "K+";
                ?>
                        <div class="top-product-item text-center me-3">
                            <div class="position-relative card">
                                <!-- Product Image -->
                                <a href='view_product.php?ProductID=<?php echo $row_top["ProductID"]; ?>' class='d-block'>
                                    <img src='../admin/management/<?php echo $row_top["Photo"]; ?>' alt='Top Product Image' class='img-fluid'>
                                </a>
                                <!-- "TOP" Badge -->
                                <div class="rankbadge">
                                    <span class="position-absolute rounded-0">TOP</span>
                                </div>
                            </div>
                            <p class='product-name fw-semibold m-0' style="color: #4a525a; word-wrap: break-word; white-space: normal;"><?php echo $row_top["ProductName"]; ?></p>
                            <!--<p class='text-muted small'>Monthly Sales <?php echo $monthly_sales; ?></p>-->
                        </div>
                <?php
                    }
                } else {
                    echo "<p class='col'>No top products found</p>";
                }
                ?>
            </div>
        </div>

        <div class="product-label rounded-0 mt-4 pt-3">
            <h6 class="text-success text-center fw-semibold">DAILY DISCOVER</h6>
        </div>
        <div class="product rounded-0">
            <div class="row row-cols-md-5 row-cols-sm-2 row-cols-2 g-2 mt-3 px-md-2">
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
                                    <p class='card-text product-price fw-semibold'>&#8369;<?php echo $row["Price"]; ?></p>
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
    <footer class="text-center text-start text-light bg-secondary mt-4">
        <div class="text-center p-2" style="background-color: rgba(0, 0, 0, 0.2)">
            <small>© 2024 Copyright MAPARCO</small>
        </div>
    </footer>

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