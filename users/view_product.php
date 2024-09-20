<?php include '../connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            /* overflow-x: hidden; */
            background-color: #f8f9fa;
            /* Added background color */
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-details {
            flex-grow: 1;
            padding-bottom: 50px;
        }

        .product-image {
            max-width: 100%;
            height: auto;
        }

        .product-buttons button {
            width: 100%;
        }

        .product-buttons {
            display: flex;
            justify-content: end;
        }

        .product-image-container img {
            display: flex;
        }

        .BOX {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        .row {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .review-card {
            height: 100%;
            /* Ensure review cards take full height */
        }

        @media (max-width: 510px) {
            .product-info h2{
                font-size: 18px;
            }
            .product-info p,
            .product-info label,
            .product-info price{
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbars/navbar-vproduct.php'; ?>
    <div class="container">
        <div class="product-details">
            <div class="BOX">
                <?php
                if (isset($_GET['ProductID'])) {
                    $productID = $_GET['ProductID'];
                    $sql = "SELECT * FROM products WHERE ProductID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $productID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                ?>
                        <div class="col-sm-12 p-3 bg-success bg-opacity-10 border border-success border-start rounded-0">
                            <div class="d-flex flex-column flex-md-row">
                                <div class="product-image-container me-md-4">
                                    <img src="../admin/management/<?php echo $row['Photo']; ?>" alt="Product Image" class="product-image" width="300px">
                                </div>
                                <div class="product-info p-2">
                                    <h2 class="product-title"><?php echo $row['ProductName']; ?></h2>
                                    <p class="product-price text-danger">&#8369;<?php echo $row['Price']; ?></p>
                                    <p class="product-description"><?php echo $row['Description']; ?></p>
                                    <div class="product-quantity mb-3">
                                        <label for="quantity">Quantity:</label>
                                        <div class="input-group" style="width: 120px;">
                                            <button class="input-group-text decrement-btn">-</button>
                                            <input type="text" class="form-control text-center input-qty bg-white" id="quantityInput" value="1">
                                            <button class="input-group-text increment-btn">+</button>
                                        </div>
                                        <div class="stock">
                                            <p><?php echo $row['QuantityAvailable']; ?> pieces available</p>
                                        </div>
                                    </div>
                                    <div class="product-buttons d-flex gap-2">
                                        <button class="product-buy-button btn btn-outline-danger btn-md" data-product-id="<?php echo $productID; ?>"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                                        <button class="product-buy-now-button btn btn-success btn-md"><i class="bi bi-cart-plus"></i> Buy Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    } else {
                        echo "<p>Product not found</p>";
                    }
                } else {
                    echo "<p>ProductID not provided</p>";
                }
                ?>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <h4 class="fw-bold text-success">Product Reviews</h4>
                <?php
                if (isset($_GET['ProductID'])) {
                    $productID = $_GET['ProductID'];
                    $sql = "SELECT r.*, c.ProfilePicFilename AS ProfilePicFilename, c.Name as CustomerName
                FROM reviews r 
                JOIN customers c ON r.CustomerID = c.CustomerID 
                WHERE r.ProductID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $productID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $review_stars = $row['Rating'];
                            $full_stars = floor($review_stars);
                            $empty_stars = 5 - $full_stars;
                ?>
                            <div class="col-md-6 mb-4">
                                <div class="card review-card">
                                    <div class="card-body d-flex">
                                        <?php if (!empty($row['ProfilePicFilename'])) : ?>
                                            <img src="users/uploads/<?php echo $row['ProfilePicFilename']; ?>" alt="Customer Profile Pic" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                        <?php else : ?>
                                            <div class="rounded-circle bg-secondary me-3" style="width: 50px; height: 50px;"></div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="card-title"><?php echo $row['CustomerName']; ?></h6>
                                            <div class="star-rating">
                                                <?php
                                                for ($i = 0; $i < $full_stars; $i++) {
                                                    echo '<i class="bi bi-star-fill text-warning"></i>';
                                                }
                                                for ($i = 0; $i < $empty_stars; $i++) {
                                                    echo '<i class="bi bi-star text-warning"></i>';
                                                }
                                                ?>
                                            </div>
                                            <div class="text-muted">(<?php echo $review_stars; ?>/5)</div>
                                            <p style="font-size:13px" class="card-text mt-2"><?php echo $row['ReviewText']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "<div class='col-12'><p>No reviews found for this product.</p></div>";
                    }
                } else {
                    echo "<div class='col-12'><p>ProductID not provided.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Warning Modal -->
    <div class="modal" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success" id="warningModalLabel">No Address Found.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Please add a default address to proceed.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary btn-sm" id="goToAddress">Go to Address</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".product-buy-button").click(function() {
                var productID = $(this).data('product-id');
                var quantity = $(this).closest('.product-info').find('.input-qty').val();
                $.ajax({
                    type: "POST",
                    url: "add_to_cart.php",
                    data: {
                        ProductID: productID,
                        Quantity: quantity
                    },
                    success: function(response) {
                        alert(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error adding product to cart. Please try again later.');
                    }
                });
            });

            $(".product-buy-now-button").click(function() {
                var productID = $(this).closest('.product-info').find('.product-buy-button').data('product-id');
                var quantity = $(this).closest('.product-info').find('.input-qty').val();
                $.ajax({
                    type: "POST",
                    url: "get_product_and_address_info.php",
                    data: {
                        ProductID: productID,
                        Quantity: quantity
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (!data.defaultAddress) {
                            $('#warningModal').modal('show');
                        } else {
                            window.location.href = "confirmation.php?data=" + encodeURIComponent(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error fetching product information and default address. Please try again later.');
                    }
                });
            });

            $(".increment-btn").click(function() {
                var inputField = $(this).closest('.input-group').find('.input-qty');
                var newValue = parseInt(inputField.val()) + 1;
                inputField.val(newValue);
            });

            $(".decrement-btn").click(function() {
                var inputField = $(this).closest('.input-group').find('.input-qty');
                var newValue = parseInt(inputField.val()) - 1;
                if (newValue >= 1) {
                    inputField.val(newValue);
                }
            });

            $("#goToAddress").click(function() {
                window.location.href = "address.php";
            });
        });
    </script>
</body>

</html>