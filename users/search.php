<?php include '../connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Search Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            /* background-color: rgb(232, 247, 232); */
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
        }

        .card-body p {
            font-size: 13px;
        }

        .product-price {
            color: red;
        }

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

        .star-rating i {
            color: #f39c12;
        }

        .product-con {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

       @media (max-width: 510px) {
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
        }
    </style>
</head>

<body>
    <?php include 'navbar_dashboard.php'; ?>

    <div class="container mt-5">
        <?php
        if (isset($_GET['query'])) {
            $query = $_GET['query'];

            // Prepare the SQL statement with table alias to avoid ambiguity
            $stmt = $conn->prepare("SELECT p.ProductID, p.Photo, p.ProductName, p.Description, p.Price, AVG(r.Rating) AS AvgRating
                                    FROM products p
                                    LEFT JOIN reviews r ON p.ProductID = r.ProductID
                                    WHERE p.ProductName LIKE ?
                                    GROUP BY p.ProductID
                                    ORDER BY RAND()");
            $searchQuery = "%" . $query . "%";
            $stmt->bind_param("s", $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<h4 class='fw-bold text-success mt-2'><i class='fa-solid fa-magnifying-glass'></i> Search results for <i>'$query'</i></h4>";

            $displayedProductIds = [];

            if ($result->num_rows > 0) {
                echo "<div class='product-con row row-cols-md-4 row-cols-sm-2 g-3 mt-2'>";
                while ($row = $result->fetch_assoc()) {
                    $displayedProductIds[] = $row["ProductID"];
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
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="bi bi-star-fill text-warning star"></i>';
                                    }
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
                echo "</div>";

                // Display related products
                $words = explode(' ', $query);
                $relatedQuery = "SELECT p.ProductID, p.Photo, p.ProductName, p.Description, p.Price, AVG(r.Rating) AS AvgRating
                                 FROM products p
                                 LEFT JOIN reviews r ON p.ProductID = r.ProductID
                                 WHERE (";
                $params = [];
                foreach ($words as $word) {
                    $relatedQuery .= "p.ProductName LIKE ? OR ";
                    $params[] = "%" . $word . "%";
                }
                $relatedQuery = rtrim($relatedQuery, ' OR ');
                $relatedQuery .= ") AND p.ProductID NOT IN (" . implode(',', array_fill(0, count($displayedProductIds), '?')) . ") GROUP BY p.ProductID
                                 ORDER BY RAND()";
                $stmt = $conn->prepare($relatedQuery);

                $types = str_repeat('s', count($params)) . str_repeat('i', count($displayedProductIds));
                $stmt->bind_param($types, ...array_merge($params, $displayedProductIds));
                $stmt->execute();
                $relatedResult = $stmt->get_result();

                if ($relatedResult->num_rows > 0) {
                    echo "<h4 class='fw-bold text-success mt-5'>Related Products</h4>";
                    echo "<div class='product-con row row-cols-md-4 row-cols-sm-2 g-3 mt-2'>";
                    while ($row = $relatedResult->fetch_assoc()) {
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
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<i class="bi bi-star-fill text-warning star"></i>';
                                        }
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
                    echo "</div>";
                }
            } else {
                echo "<p>No products found for '$query'</p>";

                // Query for random products to recommend
                $stmt = $conn->prepare("SELECT p.ProductID, p.Photo, p.ProductName, p.Description, p.Price, AVG(r.Rating) AS AvgRating
                                        FROM products p
                                        LEFT JOIN reviews r ON p.ProductID = r.ProductID
                                        GROUP BY p.ProductID
                                        ORDER BY RAND()
                                        LIMIT 4");
                $stmt->execute();
                $recommendationResult = $stmt->get_result();

                if ($recommendationResult->num_rows > 0) {
                    echo "<h4 class='fw-bold text-success mt-5'>Recommended Products</h4>";
                    echo "<div class='product-con row row-cols-md-4 row-cols-sm-2 g-3 mt-2'>";
                    while ($row = $recommendationResult->fetch_assoc()) {
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
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<i class="bi bi-star-fill text-warning star"></i>';
                                        }
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
                    echo "</div>";
                } else {
                    echo "<p>No products available for recommendation.</p>";
                }
            }

            $stmt->close();
        } else {
            echo "<p>No search query provided.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>

</html>