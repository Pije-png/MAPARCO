<?php include '../connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
            margin-top: 20px;
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.1);
            padding: 5px;
            border-radius: 10px;
        }

        .product-image {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            border-bottom: 1px solid #c1c1c1;
            margin-bottom: 10px;
        }

        .card-body {
            text-align: left;
            padding: 10px;
            font-size: 14px;
            max-width: 150px;
        }

        .card-body p {
            font-size: 13px;
        }

        .product-price {
            color: red;
        }

        /* Grid and responsive styles */
        .card {
            padding: 10px;
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid transparent;
            transition: transform 0.3s ease;
            text-decoration: none;
            min-height: 240px;
            max-height: 240px;
        }

        .card:hover {
            border: 1px solid red;
            text-decoration: none;
            transform: translateY(-5px);
        }

        .carousel-item img {
            max-width: 100%;
            margin-top: 40px;
        }

        .star-rating i {
            color: #f39c12;
        }

        @media (max-width: 510px) {

            /* Adjust font size for smaller screens */
            .card-body p {
                font-size: 8px;
                line-height: 8px;
            }

            .card {
                padding: 5px;
                min-height: 160px;
                max-height: 160px;
                width: 80px;

            }

            .product-image {
                max-height: 60px;
                margin-bottom: 5px;
            }

            .container {
                margin-top: 12px;
            }

            .star,
            .text-muted {
                font-size: 10px;
            }
            
            .div h3{
                font-size:20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar_dashboard.php'; ?>
    <div class="content-container">
        <div class="container">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../img/bgs/slide1.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../img/bgs/slide2.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="../img/bgs/slide3.png" class="d-block w-100" alt="...">
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

            <div class="product mt-3 bg bg-light p-3">
                <div class="div">
                    <h3 class="fw-bold text-success">Our Products</h3>
                </div>
                <div class="row row-cols-md-5 row-cols-sm-2 g-2 mt-2 px-md-5 px-sm-2">
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
                                <a href='view_product.php?ProductID=<?php echo $row["ProductID"]; ?>' class='card grid-item'>
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
    <footer class="mt-1" style="background-color: limegreen; color: #fff; padding: 40px 0; text-align: center;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between;">
    
    <!-- Company Info Section -->
    <div style="flex: 1 1 200px; margin: 20px;">
          <h4 style="margin-bottom: 15px; font-size: 18px;">About Us</h4>
          <p style="line-height: 1.6; font-size:13px">
           At MAPARCO, we are dedicated to enhancing your online shopping experience. Our mission is to provide exceptional service and innovative solutions, ensuring you find exactly what you're looking for with ease and confidence. We are committed to quality, convenience, and customer satisfaction, striving to make every purchase a seamless and enjoyable journey.
          </p>
        </div>
    
    <!-- Contact Information Section -->
    <div style="flex: 1 1 200px; margin: 20px;">
      <h4 style="margin-bottom: 15px; font-size: 18px;">Contact Us</h4>
      <p style="line-height: 1.6; font-size:13px">
        Address: Sitio Dos, Mahabang Parang, Naujan, Oriental Mindoro, Philippines<br>
        Phone: (+63)916 7466 766<br>
        Email: tapaspatrickjames@gmail.com
      </p>
    </div>
    
    <!-- Social Media Section -->
    <div style="flex: 1 1 200px; margin: 20px;">
      <h4 style="margin-bottom: 15px; font-size: 18px;">Follow Us</h4>
      <div style=" font-size:13px">
        <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Facebook</a>
        <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Twitter</a>
        <a href="#" style="color: #fff; margin-right: 10px; text-decoration: none;">Instagram</a>
      </div>
    </div>
  </div>
  
  <!-- Copyright Section -->
  <div style="border-top: 1px solid #444; margin-top: 40px; padding-top: 20px;">
    <p>&copy; 2024 MAPARCO. All rights reserved.</p>
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
