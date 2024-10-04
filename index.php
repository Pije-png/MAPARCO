<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MAPARCO";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product data and average rating from the database
$sql = "SELECT p.ProductID, p.Photo, p.ProductName, p.Description, p.Price, 
        IFNULL(AVG(r.Rating), 0) AS AvgRating
        FROM products p
        LEFT JOIN reviews r ON p.ProductID = r.ProductID
        GROUP BY p.ProductID";
$result = $conn->query($sql);

$products = array(); // Array to store product data

if ($result->num_rows > 0) {
    // Fetching product data and storing it in the array
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    // Shuffle the array to randomize the order of products
    shuffle($products);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-adsense-account" content="ca-pub-3347241354173382">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>MAPARCO</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            font-family: "Poppins", sans-serif;
            box-sizing: border-box;
        }

        .product-image {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            border-bottom: 1px solid #c1c1c1;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .card-body {
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }

        .card:hover {
            transform: translateY(-5px);
            border: 1px solid red;
            text-decoration: none;
        }

        .sliding {
            background-image: url('img/bg.png');
            width: 100%;
            height: 400px;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sliding .text {
            color: black;
            text-align: center;
            font-size: 18px;
            width: 700px;
            background-color: rgba(248, 249, 250, 0.50);
        }

        .text a {
            margin-top: 20px;
            width: 30%;
            padding: 10px;
        }
    </style>

    <style>
        /* ========= modals ========== */
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            font-size: 12px;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 1% auto;
            padding: 30px;
            border: 1px solid #888;
            text-align: justify;
            position: relative;
            width: 80%;
            /* Default width for larger screens */
            max-width: 400px;
            /* Maximum width for larger screens */
        }

        .modal-content .continue-button {
            display: block;
            margin: 0 auto;
            font-size: 13px;
        }

        .modal-content h5 {
            color: #888;
            font-weight: bolder;
        }

        .modal-content .close {
            color: darkred;
            float: right;
            font-size: 28px;
            font-weight: bold;
            text-align: right;
        }

        .modal-content .close:hover,
        .modal-content .close:focus {
            color: red;
            text-decoration: none;
            cursor: pointer;
        }

        /* ============================= */
        .load button {
            display: block;
            width: 387px;
            height: 40px;
            margin: 0 auto;
            font-family: Roboto-Medium;
            font-size: 14px;
            line-height: 40px;
            color: #1a9cb8;
            text-align: center;
            text-transform: uppercase;
            cursor: pointer;
            border: 1px solid #1a9cb8;
        }

        .load button:hover {
            background-color: lightblue;
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
                padding: 10px;
                min-height: 170px;
                max-height: auto;
                width: 100%;
            }

            .product-image {
                max-height: 80px;
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

        /* ============================== */
    </style>

</head>

<body>
    <?php include 'navbar-index.php'; ?>

    <div class="container-fluid mt-1 pt-5">
        <div class="sliding">
            <div class="text p-3">
                <h2>So deliciously easy.</h2>
                <p>Discover the sweet sensation of Natta de Coco! Delightful, chewy, and refreshing.</p>
                <p class="mb-0">Try it now for a taste adventure!</p>
                <a href='login.php' class='btn btn-md btn-success rounded-0'>GET STARTED</a>
            </div>
        </div>
        <div class="product bg-success mt-3 p-3 bg-opacity-10 border border-success border-start rounded-0">
            <div class="div">
                <h3 class="text-success text-center">Products</h3>
            </div>
            <div class="row row-cols-md-5 row-cols-sm-2 row-cols-2 g-2 mt-2 px-md-5 px-sm-2">
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $avg_rating = round($product["AvgRating"], 1);
                        $full_stars = intval($avg_rating);
                        $empty_stars = 5 - $full_stars;

                        echo "<div class='col'>";
                        echo "<div class='card h-100' onclick='showModal(" . json_encode($product) . ")'>";
                        echo "<img src='admin/management/" . $product["Photo"] . "' alt='Product Image' class='card-img-top product-image'>";
                        echo "<div class='card-body'>";
                        echo "<p class='card-title'>" . $product["ProductName"] . "</p>";
                        echo "<div class='star-rating'>";
                        for ($i = 0; $i < $full_stars; $i++) {
                            echo '<i class="bi bi-star-fill text-warning"></i>';
                        }
                        for ($i = 0; $i < $empty_stars; $i++) {
                            echo '<i class="bi bi-star"></i>';
                        }
                        echo "<span class='text-muted'>($avg_rating/5)</span>";
                        echo "</div>";
                        echo "<p class='text-danger'>&#8369;" . $product["Price"] . "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='col'>No products found</p>";
                }
                $conn->close();
                ?>
            </div>

        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="productInfo"></div>
        </div>
    </div>

    <script>
        var modal = document.getElementById("productModal");
        var span = document.getElementsByClassName("close")[0];

        function showModal(product) {
            var productInfo = document.getElementById("productInfo");
            productInfo.innerHTML = "<h5>" + product.ProductName + "</h5>" +
                "<p><strong>Price:</strong> &#8369;" + product.Price + "</p>" +
                "<img src='admin/management/" + product.Photo + "' alt='Product Image' style='width:100%;'><br>" +
                "<p><strong>Description:</strong> " + product.Description + "</p>" +
                "<br><a href='login.php' class='login_continue'><button class='btn btn-md btn-danger' style='width:100%'>Login to Continue</button></a>";
            modal.style.display = "block";
        }

        // Close the modal when the close button is clicked
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>