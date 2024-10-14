<?php
session_start(); // Start the session
include('../connection.php');

// Check if the customer is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Please log in to view your cart.";
    exit;
}

$customerID = $_SESSION['customer_id'];

// Retrieve cart items from the database
$sql = "SELECT products.ProductID, products.ProductName, products.Photo, cart.Quantity, products.Price, (cart.Quantity * products.Price) AS Subtotal
        FROM cart
        INNER JOIN products ON cart.ProductID = products.ProductID
        WHERE cart.CustomerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

$totalCartValue = 0; // Initialize total cart value
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .header-container {
            margin: 80px auto;
            background-color: #fffefb;
            border: 1px solid rgba(224, 168, 0, .4);
            border-radius: 2px;
            border-radius: 3px;
            box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            margin-bottom: 15px;
        }

        .header-container h5 {
            color: green;
            font-weight: 500;
            margin: 0;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #000;
        }

        table thead tr th,
        table tbody tr td {
            font-size: 12px;
            /* border: 1px solid black; */
        }

        table tbody tr td img {
            width: 50px;
            height: 50px;
        }


        .table-container {
            max-height: 500px;
            min-height: 500px;
            border-bottom: 2px solid lightblue;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .quantity-control {
            display: flex;
            align-items: center;
        }

        .quantity-input {
            max-width: 60px;
            padding: 5px 10px;
            text-align: center;
        }
    </style>
    <style>
        /*@media only screen and (max-width: 600px) and (min-device-width: 320px) and (max-device-width: 768px) and (-webkit-min-device-pixel-ratio: 3) {*/
        @media (max-width: 510px) {

            table thead tr th,
            table tbody tr td,
            table tfoot tr th {
                font-size: 10px;
                padding: 0;
                /* border: 1px solid #000; */
            }

            table tbody tr td.hstack button {
                font-size: 10px;
                padding: 5px;
            }

            table tbody tr td img {
                width: 30px;
                height: 30px;
            }

            .quantity-input {
                max-width: 40px;
                padding: 5px 10px;
                text-align: center;
            }

            .input-group button,
            .input-group input {
                font-size: 10px;
                padding: 3px 5px;
            }

            .header-container {
                padding: 0.35rem 0.50rem;
            }

            .header-container h5 {
                font-size: 15px;
            }

        }
    </style>
<body>
    <?php include 'navbars/navbar.php' ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>
    <div class="cart-container vh-100">
        <div class="container">
            <div class="container header-container">
                <h5><i class="fa-solid fa-cart-arrow-down"></i> Shopping Cart</h5>
            </div>
            <div class="table-container col-12">
                <table class="table mt-1">
                    <thead class="table-success">
                        <tr>
                            <th width="20px">Product</th>
                            <th>Name</th>
                            <th>Unit_Price</th>
                            <th>Quantity</th>
                            <th>Total_Price</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $totalCartValue += $row['Subtotal'];
                                echo "<tr>";
                                echo "<td class='hstack'>
                                <button class='btn btn-outline-primary btn-sm rounded-5' onclick='removeFromCart(" . $row['ProductID'] . ")'><i class='fa-regular fa-trash-can'></i></button>
                                <img src='../admin/management/" . $row['Photo'] . "' alt='Product Image'>
                                </td>";
                                echo "<td> <a href='view_product.php?ProductID=" . $row['ProductID'] . "'>" . $row['ProductName'] . "</a></td>";
                                echo "<td class='unit-price text-danger'>₱" . $row['Price'] . "</td>";
                                echo "<td>
                                    <div class='quantity-control input-group'>
                                        <button class='input-group-text decrement-btn' onclick='changeQuantity(this, -1)'>-</button>
                                        <input type='text' class='quantity-input form-control' value='" . $row['Quantity'] . "' min='1' onchange='changeQuantity(this, 0)'>
                                        <button class='input-group-text increment-btn' onclick='changeQuantity(this, 1)'>+</button>
                                    </div>
                                  </td>";
                                echo "<td class='total-price text-danger'>₱" . $row['Subtotal'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr>
                            <td colspan='6' class='text-center'>
                             <img src='users/mr3.png' alt='No cancelled orders' style='width:300px; height:auto;'>
                            <h3>Cart is empty</h3>
                            </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th>
                            <th colspan="1" class="text-danger">₱ <?php echo $totalCartValue; ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        function removeFromCart(productID) {
            if (confirm("Are you sure you want to remove this item from the cart?")) {
                $.ajax({
                    url: 'remove_from_cart.php',
                    method: 'POST',
                    data: {
                        productID: productID
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error removing item from cart: ' + error);
                    }
                });
            }
        }

        function changeQuantity(element, change) {
            const input = element.parentNode.querySelector('.quantity-input');
            let value = parseInt(input.value) + change;
            if (value < 1) value = 1;
            input.value = value;
            updateTotalPrice(element.parentNode);
        }

        function updateTotalPrice(container) {
            const unitPrice = parseFloat(container.parentNode.previousElementSibling.textContent.replace('₱', ''));
            const quantity = parseInt(container.querySelector('.quantity-input').value);
            const totalPriceElement = container.parentNode.nextElementSibling;
            const totalPrice = unitPrice * quantity;
            totalPriceElement.textContent = '₱' + totalPrice.toFixed(2);
        }
    </script>
</body>

</html>