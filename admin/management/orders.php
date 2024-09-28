<?php
include '../../connection.php';

// Update order status
if (isset($_POST['update_status'])) {
    $orderID = $_POST['order_id'];
    $newOrderStatus = $_POST['new_order_status'];

    // Update order status in the database
    $update_status_sql = "UPDATE orders SET OrderStatus = '$newOrderStatus' WHERE OrderID = '$orderID'";
    if ($conn->query($update_status_sql) === TRUE) {
        $order_status_message = "Order status updated successfully";
    } else {
        $order_status_message = "Error updating order status: " . $conn->error;
    }
}

// Update payment status
if (isset($_POST['update_payment_status'])) {
    $orderID = $_POST['order_id'];
    $newPaymentStatus = $_POST['new_payment_status'];

    // Update payment status in the database
    $update_payment_status_sql = "UPDATE orders SET PaymentStatus = '$newPaymentStatus' WHERE OrderID = '$orderID'";
    if ($conn->query($update_payment_status_sql) === TRUE) {
        $payment_status_message = "Payment status updated successfully";
    } else {
        $payment_status_message = "Error updating payment status: " . $conn->error;
    }
}

// SQL query to fetch order data with customer names and addresses
$sql = "SELECT o.*, a.FullName AS CustomerName, a.Description, a.HouseNo, a.Street, a.Barangay, a.City, a.Province, a.ZipCode 
        FROM orders o 
        JOIN addresses a ON o.AddressID = a.AddressID";
$result = $conn->query($sql);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        .table th {
            text-align: left;
            width: 12%;
            padding: 10px;
            background-color: #8fd19e;
        }

        table,
        tr,
        td {
            border-collapse: collapse;
            border-top: 1px solid #dee2e6;
            padding: 0;
            margin: 0;
            font-size: 12px;
            padding: 10px;
        }

        .product-container,
        .order-container,
        .customer-container {
            margin: 20px;
        }

        .table {
            background: var(--body-color);
            padding: 30px;
            /* background-color: #fff; */
            margin: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.1s ease-in-out;
            font-size: 13px;
        }

        .TotalAmount {
            color: red;
        }

        /* =================================================== */
        /* =================================================== */
        /* =================================================== */
        /* Styles for update and delete links */
        a.update-link,
        a.delete-link {
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        /* ================================================ */
        /* ================================================ */
        /* ================================================ */
        .HLRhQ8 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .btn {
            background-color: #04AA6D;
            font-family: 'Source Sans Pro', sans-serif;
            color: white;
            padding: 6px 15px;
            margin-top: 4px;
            margin-right: 10px;
            display: block;
            float: right;
            border-radius: 5px;
            font-size: 12px;
        }

        .btn-normal {
            background-color: DodgerBlue;
            font-family: 'Source Sans Pro', sans-serif;
            color: white;
            padding: 6px 15px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
        }

        .btn:hover {
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.9);
        }

        /* =================================================== */
        /* =================================================== */
        /* =================================================== */
        /* Add styles for select dropdowns */
        /* Add styles for update buttons */
        button[name='update_status'],
        button[name='update_payment_status'] {
            padding: 5px 8px;
            background-color: #04AA6D;
            color: white;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        button[name='update_status']:hover,
        button[name='update_payment_status']:hover {
            background-color: #028e5a;
        }

        /* ===================================================== */
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
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #888;
            max-width: 350px;
            font-size: 12px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* ======================================================================== */
        /* ======================================================================== */
        /* ======================================================================== */
        .order-status-pending {
            color: orange;
        }

        .order-status-processing {
            color: blue;
        }

        .order-status-shipped {
            color: #15BE2F;
        }

        .order-status-delivered {
            color: #888;
        }

        .payment-status-pending {
            color: orange;
        }

        .payment-status-paid {
            color: #888;
        }

        /* ============================================================= */
        .orders-table-container {
            max-height: 450px;
            overflow-y: auto;
        }

        .admin-dashboard {
            width: 100%;
            border-bottom: 1px solid #dee2e6;
            border-collapse: collapse;
            border-top: 1px solid #dee2e6;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <?php include 'navbar.php'; ?>

    <section class="home">
        <div class="order-container">
            <div class="table">
                <div class="HLRhQ8">
                    <h2>Orders</h2>
                </div>
                <div class="orders-table-container">
                    <div class="status-messages">
                        <?php if (isset($order_status_message)) {
                            echo "<div class='status-message'>$order_status_message</div>";
                        } ?>
                        <?php if (isset($payment_status_message)) {
                            echo "<div class='status-message'>$payment_status_message</div>";
                        } ?>
                    </div>
                    <div class="header-container">
                        <table class="admin-dashboard">
                            <tr>
                                <th>Name</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Shipping Address</th>
                                <!-- <th>Billing Address</th> -->
                                <th>ORDER</th>
                                <th>PAYMENT</th>
                            </tr>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["CustomerName"] . "</td>";
                                    echo "<td>" . $row["OrderDate"] . "</td>";
                                    echo "<td class='TotalAmount'>₱" . $row["TotalAmount"] . "</td>";
                                    echo "<td class='order-status-" . strtolower(str_replace(' ', '-', $row["OrderStatus"])) . "'>" . $row["OrderStatus"] . "</td>";
                                    echo "<td class='payment-status-" . strtolower(str_replace(' ', '-', $row["PaymentStatus"])) . "'>" . $row["PaymentStatus"] . "</td>";
                                    echo "<td> <button  class='btn-normal' onclick='displayShippingAddress(\"" . $row["Description"] . ", " . $row["HouseNo"] . ", " . $row["Street"] . ", " . $row["Barangay"] . ", " . $row["City"] . ", " . $row["Province"] . ", " . $row["ZipCode"] . "\")'>View</button></td>";
                                    echo "<td>";
                                    // Update Order Status Form
                                    echo "<form method='post'>";
                                    echo "<input type='hidden' name='order_id' value='" . $row["OrderID"] . "'>";
                                    echo "<select name='new_order_status'>";
                                    echo "<option value='' disabled selected>ORDER</option>";
                                    echo "<option value='Pending'>Pending</option>";
                                    echo "<option value='Processing'>Processing</option>";
                                    echo "<option value='Shipped'>Shipped</option>";
                                    echo "<option value='Delivered'>Delivered</option>";
                                    echo "</select>";
                                    echo "<button type='submit' name='update_status'>Update</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    // Update Payment Status Form
                                    echo "<td>";
                                    echo "<form method='post'>";
                                    echo "<input type='hidden' name='order_id' value='" . $row["OrderID"] . "'>";
                                    echo "<select name='new_payment_status'>";
                                    echo "<option value='' disabled selected>PAYMENT</option>";
                                    echo "<option value='Pending'>Pending</option>";
                                    echo "<option value='Paid'>Paid</option>";
                                    echo "</select>";
                                    echo "<button type='submit' name='update_payment_status'>Update</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No orders found</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal HTML -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close"></span>
                    <p id="shippingAddressContent"></p>
                </div>
            </div>
    </section>

    <script>
        // Function to display modal with shipping address
        function displayShippingAddress(address) {
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // Display shipping address in the modal
            var addressContent = document.getElementById("shippingAddressContent");
            addressContent.innerHTML = "<strong>Shipping Address:</br></strong> " + address;

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Display the modal
            modal.style.display = "block";
        }
    </script>
    <script>
        const toggle = document.querySelector(".toggle");
        const sidebar = document.querySelector(".sidebar");
        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });
    </script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>