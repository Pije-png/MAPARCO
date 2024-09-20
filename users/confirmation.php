<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connection.php');

// Retrieve data passed from the previous page
$data = json_decode($_GET['data'], true);

// Display product information and default address for confirmation
$productInfo = $data['productInfo'];
$defaultAddress = $data['defaultAddress'];
$quantity = $data['quantity'];  // Retrieve the quantity from the data array

// Calculate total price
$totalPrice = $productInfo['Price'] * $quantity;

// Initialize shipping method variable
$shippingMethod = '';

// Check if shipping method is set in $_POST
if (isset($_POST['shipping_method'])) {
    $shippingMethod = $_POST['shipping_method'];
    // Adjust total price based on shipping method
    if ($shippingMethod === 'door_to_door') {
        $totalPrice += 58; // Additional cost for Door-to-Door Delivery
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin: auto;
            margin-top: 80px;
        }

        .container p,
        .container a {
            font-size: 13px;
        }

        .header-container {
            background-color: #fffefb;
            border: 1px solid rgba(224, 168, 0, .4);
            padding: 20px 50px;
        }
    </style>
    <style>
        .header {
            display: flex;
            justify-content: space-around;
        }

        .confirm-card {
            background-color: #fffefb;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead td {
            font-size: 14px;
            padding: 8px;
        }

        tbody td {
            font-size: 13px;
            padding: 8px;
        }

        .payment-container {
            border: 1px solid rgba(224, 168, 0, .4);
            padding: 10px 20px;
            margin-bottom: 15px;
            margin-top: 15px;
        }

        .payment-container p,
        .payment-container label,
        .payment-container select {
            font-size: 12px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .payment-container h6 {
            color: green;
            font-weight: 500;
            margin: 0;
            margin-bottom: 20px;
        }

        /*@media only screen and (max-width: 600px) and (min-device-width: 320px) and (max-device-width: 768px) {*/
        @media (max-width: 510px) {
            .header {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbars/navbar.php'; ?>

    <div class="container">
        <div class="header-container">
            <h5 class="text-success"><i class="fa-solid fa-location-dot"></i> Delivery Address</h5>
            <div class="header">
                <div class="personal-info">
                    <p><strong class="h5 fw-bold"><?php echo $defaultAddress['FullName']; ?></strong><br>
                    <?php echo $defaultAddress['PhoneNumber']; ?></p>
                </div>
                <div class="addresses">
                    <p><?php echo $defaultAddress['Description']; ?> <strong class="text-success">(Description)</strong><br>
                        <?php echo $defaultAddress['HouseNo']; ?>, <?php echo $defaultAddress['Street']; ?>,
                        <?php echo $defaultAddress['Barangay']; ?>,
                        <?php echo $defaultAddress['City']; ?>, <?php echo $defaultAddress['Province']; ?>,
                        <?php echo $defaultAddress['ZipCode']; ?>
                        <strong class="text-success">(Address)</strong></p>
                    <label class='default-label btn btn-outline-success btn-sm rounded-0'>Default</label>
                </div>
                <!-- <div>
                    <a href="address.php" class="btn btn-primary">Change</a>
                </div> -->
            </div>
        </div>
    </div>

    <div class="confirm-card col-10 p-3 mt-3 mb-3">
        <table>
            <thead>
                <tr>
                    <td class="h6">Products Ordered</td>
                    <td class="text-muted">Unit Price</td>
                    <td class="text-muted">Quantity</td>
                    <td class="text-muted">Item Subtotal</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $productInfo['ProductName']; ?></td>
                    <td>&#8369;<?php echo $productInfo['Price']; ?></td>
                    <td><?php echo $quantity; ?></td>
                    <td>&#8369;<?php echo $totalPrice; ?></td>
                </tr>
            </tbody>
        </table>
        <form action="finalize_purchase.php" method="post">
            <input type="hidden" name="ProductID" value="<?php echo $productInfo['ProductID']; ?>">
            <input type="hidden" name="Quantity" value="<?php echo $quantity; ?>">

            <div class="payment-container">
                <h6>Payment Method</h6>
                <div class="payment-method">
                    <div class="shipping-method">
                        <div class="form-group">
                            <label for="shipping_method" class="text-secondary">Shipping Method:</label>
                            <select name="shipping_method" id="shipping_method" class="form-control" onchange="calculateTotal()">
                                <option value="" disabled selected>Select</option>
                                <option value="gcash" disabled>G-cash (Unavailable)</option>
                                <option value="door_to_door">Cash-on-Delivery (Shipping Fee ₱58)</option>
                                <option value="self_pickup">Self Pick-up (₱0)</option>
                            </select>
                        </div>
                    </div>
                    <div class="total-payment">
                        <p class="text-secondary">Merchandise Subtotal: ₱<?php echo $productInfo['Price']; ?></p>
                        <p class="text-secondary">Shipping Total: <span id="shipping_total">₱0</span></p>
                        <p class="text-secondary">Total Payment: <strong id="total_payment" class="h6 text-danger"> ₱<?php echo $totalPrice; ?></strong></p>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <!-- <a href="view_product.php" class="btn btn-light">Cancel</a> -->
                <button type="submit" class="btn btn-confirm btn-danger">Place order</button>
            </div>
        </form>
    </div>

    <script>
        function calculateTotal() {
            var shippingMethod = document.getElementById("shipping_method").value;
            var totalPrice = <?php echo $totalPrice; ?>;
            var shippingTotal = 0;

            if (shippingMethod === 'door_to_door') {
                shippingTotal = 58;
            } else if (shippingMethod === 'self_pickup') {
                shippingTotal = 0;
            }

            var totalPayment = totalPrice + shippingTotal;

            document.getElementById("shipping_total").innerText = '₱' + shippingTotal;
            document.getElementById("total_payment").innerText = '₱' + totalPayment;
        }
    </script>

</body>

</html>