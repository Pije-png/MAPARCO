<?php
include '../../../connection.php';

// Update order status
if (isset($_POST['update_status'])) {
    if (isset($_POST['new_order_status']) && !empty($_POST['new_order_status'])) {
        $orderID = $_POST['order_id'];
        $newOrderStatus = $_POST['new_order_status'];

        // Update order status in the database
        $update_status_sql = "UPDATE orders SET OrderStatus = '$newOrderStatus' WHERE OrderID = '$orderID'";
        if ($conn->query($update_status_sql) === TRUE) {
            $order_status_message = "Order status updated successfully!";
        } else {
            $order_status_message = "Error updating order status: " . $conn->error;
        }
    } else {
        $order_status_message = "Note: Please select a new order status!";
    }
}

// Update payment status
if (isset($_POST['update_payment_status'])) {
    if (isset($_POST['new_payment_status']) && !empty($_POST['new_payment_status'])) {
        $orderID = $_POST['order_id'];
        $newPaymentStatus = $_POST['new_payment_status'];

        // Update payment status in the database
        $update_payment_status_sql = "UPDATE orders SET PaymentStatus = '$newPaymentStatus' WHERE OrderID = '$orderID'";
        if ($conn->query($update_payment_status_sql) === TRUE) {
            $payment_status_message = "Payment status updated successfully!";
        } else {
            $payment_status_message = "Error updating payment status: " . $conn->error;
        }
    } else {
        $payment_status_message = "Note: Please select a new payment status!";
    }
}

// SQL query to fetch cancelled orders data with customer names and addresses
$sql = "SELECT o.*, a.FullName AS CustomerName, a.Description, a.HouseNo, a.Street, a.Barangay, a.City, a.Province, a.ZipCode 
        FROM orders o 
        JOIN addresses a ON o.AddressID = a.AddressID
        WHERE o.OrderStatus = 'Cancelled'";
$result = $conn->query($sql);

// Get the count of cancelled orders
$cancelled_count_sql = "SELECT COUNT(*) AS cancelled_count FROM orders WHERE OrderStatus = 'Cancelled'";
$cancelled_count_result = $conn->query($cancelled_count_sql);
$cancelled_count = $cancelled_count_result->fetch_assoc()['cancelled_count'];

// Get the number of products
$cancelled_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <link rel="stylesheet" href="Orders.css">
    <title>Cancelled Orders</title>
    <style>
        .admin-dashboard {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 0; */
        }

        table {
            border-collapse: collapse;
        }

        table tr,
        table th,
        table td {
            font-size: 12px;
            border: 1px solid #999;
        }

        table tr,
        table th {
            padding: 5px;
        }

        thead {
            background-color: #98FB98;
        }

        .column {
            margin-bottom: 0;
        }

        .editbtn {
            width: 100%;
        }

        .order-status-processing {
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

        .payment-status-processing {
            color: orange;
        }

        .payment-status-paid {
            color: #888;
        }

        input[type="checkbox"] {
            transform: scale(1.5);
        }
    </style>
</head>

<body class="bg bg-light">

    <?php include 'sidebar-orders.php'; ?>

    <section class="home">
        <div class="order-container">
            <div class="container-fluid">
                <div class="head pt-3">
                    <h4 class="text-center">List of Cancelled</h4>
                </div>
                <div class="column">
                    <div class="status-messages">
                        <?php if (isset($global_update_message)) {
                            echo "<div class='status-message'>" . htmlspecialchars($global_update_message) . "</div>";
                        } ?>
                    </div>
                </div>
                <div class="orders-table-container">
                    <div class="header-container">
                        <?php
                        // Check if there are cancelled orders
                        if ($cancelled_count > 0) {
                            echo '<table class="admin-dashboard">';
                            echo '<thead>';
                            echo '<tr class="fw-bold fs-5 bg bg-success text-light">';
                            echo '<th colspan="7">Cancelled
            <span style="font-size: 12px;" class="badge text-bg-danger">' . $cancelled_count . '</span>
          </th>';
                            echo '</tr>';
                            echo '<tr class="text-center">';
                            echo '<th style="width:2%"></th>';
                            echo '<th>Name</th>';
                            echo '<th>Order Date</th>';
                            echo '<th>Total Amount</th>';
                            echo '<th>Order Status</th>';
                            echo '<th>Shipping Address</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            $row_counter = 1; // Initialize the row counter
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row_counter++ . "</td>";
                                echo "<td>" . htmlspecialchars($row["CustomerName"]) . "</td>";
                                echo "<td>" . date("F j, Y", strtotime($row["OrderDate"])) . "</td>";
                                echo "<td class='TotalAmount'>₱" . htmlspecialchars($row["TotalAmount"]) . "</td>";
                                echo "<td class='order-status-" . strtolower(str_replace(' ', '-', $row["OrderStatus"])) . "'>" . htmlspecialchars($row["OrderStatus"]) . "</td>";
                                echo "<td> <button class='btn btn-outline-primary' onclick='displayShippingAddress(\"" . htmlspecialchars($row["Description"]) . ", " . htmlspecialchars($row["HouseNo"]) . ", " . htmlspecialchars($row["Street"]) . ", " . htmlspecialchars($row["Barangay"]) . ", " . htmlspecialchars($row["City"]) . ", " . htmlspecialchars($row["Province"]) . ", " . htmlspecialchars($row["ZipCode"]) . "\")'>View</button></td>";
                                echo "</tr>";
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            // Display placeholder image when no cancelled orders
                            echo '<div class="text-center">';
                            echo '<img src="mr3.png" alt="No cancelled orders" style="width:300px; height:auto;">';
                            echo ' <h3>No orders found.</h3>';
                            echo '</div>';  
                        }
                        ?>

                    </div>
                </div>
            </div>
            <!-- Modal HTML -->
            <div id="qrModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <p>Scan this QR Code to mark order as Delivered</p>
                    <div id="qrCodeContainer"></div>
                    <a id="downloadLink" download="QRCode.png">Download QR Code</a>
                </div>
            </div>
            <!-- Modal HTML for Shipping Address -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close btn btn-outline-danger ms-auto rounded-0" onclick="closeModal()">&times;</span>
                    <p id="shippingAddressContent"></p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Function to display modal with shipping address
        function displayShippingAddress(address) {
            var modal = document.getElementById("myModal");
            var addressContent = document.getElementById("shippingAddressContent");
            addressContent.innerHTML = "<strong>Shipping Address:</br></strong> " + address;
            modal.style.display = "block";
        }

        // Function to generate QR code and display in modal
        function generateQRCode(orderID) {
            fetch('generate_qr.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'generate_qr=true&order_id=' + orderID,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var qrModal = document.getElementById("qrModal");
                        var qrCodeContainer = document.getElementById("qrCodeContainer");
                        qrCodeContainer.innerHTML = '<img src="' + data.qrImage + '" alt="QR Code">';
                        var downloadLink = document.getElementById("downloadLink");
                        downloadLink.href = data.qrImage;
                        qrModal.style.display = "block";
                    } else {
                        alert("Error generating QR code.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            var qrModal = document.getElementById("qrModal");
            qrModal.style.display = "none";
            var myModal = document.getElementById("myModal");
            myModal.style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            var qrModal = document.getElementById("qrModal");
            var myModal = document.getElementById("myModal");
            if (event.target == qrModal || event.target == myModal) {
                qrModal.style.display = "none";
                myModal.style.display = "none";
            }
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