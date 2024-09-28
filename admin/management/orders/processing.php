<?php
include '../../../connection.php';

// processing
if (isset($_POST['update_global_status'])) {
    $orderIDs = $_POST['selected_order_ids'];  // This contains the selected order IDs as a comma-separated string
    $orderIDsArray = explode(',', $orderIDs);  // Convert to array

    $newOrderStatus = $_POST['new_global_order_status'] ?? null;
    $newPaymentStatus = $_POST['new_global_payment_status'] ?? null;

    if ($newOrderStatus || $newPaymentStatus) {
        foreach ($orderIDsArray as $orderID) {
            if ($newOrderStatus) {
                // Update order status
                $stmt = $conn->prepare("UPDATE orders SET OrderStatus = ? WHERE OrderID = ?");
                $stmt->bind_param("si", $newOrderStatus, $orderID);
                $stmt->execute();
            }

            if ($newPaymentStatus) {
                // Update payment status
                $stmt = $conn->prepare("UPDATE orders SET PaymentStatus = ? WHERE OrderID = ?");
                $stmt->bind_param("si", $newPaymentStatus, $orderID);
                $stmt->execute();
            }
        }

        $global_update_message = "Selected orders updated successfully!";
    } else {
        $global_update_message = "Please select at least one status to update.";
    }
}


// SQL query to fetch only processing orders data with customer names and addresses
$sql = "SELECT o.*, a.FullName AS CustomerName, a.Description, a.HouseNo, a.Street, a.Barangay, a.City, a.Province, a.ZipCode, 
        p.ProductName, oi.Quantity, oi.Subtotal 
        FROM orders o 
        JOIN addresses a ON o.AddressID = a.AddressID
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN products p ON oi.ProductID = p.ProductID
        WHERE o.OrderStatus = 'processing'";
$result = $conn->query($sql);

// Get the count of processing orders
$processing_count_sql = "SELECT COUNT(*) AS processing_count FROM orders WHERE OrderStatus = 'processing'";
$processing_count_result = $conn->query($processing_count_sql);
$processing_count = $processing_count_result->fetch_assoc()['processing_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/MAPARCO.png" />
    <!-- <link rel="stylesheet" href="Order.css"> -->
    <title>Orders</title>
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
    <style>
        .editbtn {
            width: 100%;
        }

        .payment-status-pending {
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
    </style>
    <!-- modal -->
    <style>
        /* The Modal (background overlay) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black with transparency */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
            max-width: 600px;
            /* Limit the width */
            border-radius: 8px;
            /* Rounded corners */
        }

        /* Form elements inside modal */
        .modal-content h4 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .modal-content .form-group {
            margin-bottom: 15px;
        }

        .modal-content select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        /* Optional: Adding responsiveness */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 90%;
                /* Adjust modal width for smaller screens */
            }
        }
    </style>
</head>

<body class="bg bg-light">

    <?php include 'sidebar-orders.php'; ?>
       

    <section class="home">
        <div class="order-container">
            <div class="container-fluid">
                <div class="head pt-3">
                    <h4 class="text-center">List of Processing</h4>
                </div>
                <div class="column">
                    <div class="status-messages">
                        <?php if (isset($global_update_message)) {
                            echo "<div class='status-message'>" . htmlspecialchars($global_update_message) . "</div>";
                        } ?>
                    </div>
                </div>
                <div class="orders-table-container">
                    <div class="header-container pb-5">
                        <table class="admin-dashboard">
                            <thead>
                                <tr class="fw-bold fs-5 bg bg-success text-light">
                                    <th colspan="8">Processing
                                        <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo htmlspecialchars($processing_count); ?></span>
                                    </th>
                                    <th colspan="2">
                                        <!-- Global Edit Button -->
                                        <button type="button" class="editbtn btn btn-sm btn-success border-0" onclick="openGlobalEditModal()">Edit</button>
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th style="width:2%"></th>
                                    <th style="width:2%">Order&nbsp;ID</th>
                                    <th>Name</th>
                                    <th>Product</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <!-- <th>Shipping Address</th> -->
                                    <th style="text-align: center;">
                                        <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" id="selectAllCheckbox" style="transform: scale(1.5);">
                                        </label>
                                    </th>
                                    <!-- <th>QR</th> -->
                                </tr>
                            </thead>

                            <tbody class="text-center bg bg-light">
                                <?php
                                $row_counter = 1;
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row_counter++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row["OrderID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["CustomerName"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["ProductName"]) . "</td>"; // Display the product name
                                        echo "<td>" . date("F j, Y", strtotime($row["OrderDate"])) . "</td>";
                                        echo "<td class='TotalAmount'>₱" . htmlspecialchars($row["TotalAmount"]) . "</td>";
                                        echo "<td class='order-status-" . strtolower(str_replace(' ', '-', $row["OrderStatus"])) . "'>" . htmlspecialchars($row["OrderStatus"]) . "</td>";
                                        echo "<td class='payment-status-" . strtolower(str_replace(' ', '-', $row["PaymentStatus"])) . "'>" . htmlspecialchars($row["PaymentStatus"]) . "</td>";
                                        echo "<td style='width:5%;'><input type='checkbox' name='order_ids[]' value='" . htmlspecialchars($row["OrderID"]) . "' class='order-checkbox' style='transform: scale(1.5);'></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>No orders found</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot class="bg bg-light">
                                <td colspan="8"></td>
                                <td colspan="2" class="text-center p-0 fs-6">
                                    <button type="button" id="clearCheckboxes" class="btn btn-outline-basic btn-sm">Clear</button>
                                </td>
                            </tfoot>
                        </table>

                        <script>
                            // Select All Checkbox functionality
                            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                                var checkboxes = document.querySelectorAll('.order-checkbox');
                                for (var checkbox of checkboxes) {
                                    checkbox.checked = this.checked;
                                }
                            });

                            // Clear individual checkboxes when the clear button is clicked
                            document.getElementById('clearCheckboxes').addEventListener('click', function() {
                                var checkboxes = document.querySelectorAll('.order-checkbox');
                                for (var checkbox of checkboxes) {
                                    checkbox.checked = false;
                                }
                                document.getElementById('selectAllCheckbox').checked = false;
                            });
                        </script>
                        <!-- Global Edit Modal -->
                        <div id="globalEditModal" class="modal">
                            <div class="modal-content">
                                <h4>Update Status</h4>
                                <form method="post" id="globalEditForm">
                                    <div class="form-group">
                                        <label for="newGlobalOrderStatus">Order Status</label>
                                        <select name="new_global_order_status" id="newGlobalOrderStatus" class="form-select form-select-sm">
                                            <option value="" disabled selected>Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="newGlobalPaymentStatus">Payment Status</label>
                                        <select name="new_global_payment_status" id="newGlobalPaymentStatus" class="form-select form-select-sm">
                                            <option value="" disabled selected>Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Paid">Paid</option>
                                        </select>
                                    </div>

                                    <!-- This hidden input will store selected order IDs -->
                                    <input type="hidden" name="selected_order_ids" id="selectedOrderIds">

                                    <div class="action-btn d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-basic" onclick="closeGlobalEditModal()">Cancel</button>
                                        <button type="submit" name="update_global_status" class="btn btn-primary">Update</button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        </table>
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
    <footer class="footer bg bg-success">
        <!-- Empty footer -->
    </footer>

    <script>
        // Function to clear all selected checkboxes
        function clearSelectedCheckboxes() {
            document.querySelectorAll('.order-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        }

        // Attach the function to the "Clear" button
        document.getElementById('clearCheckboxes').addEventListener('click', clearSelectedCheckboxes);
    </script>
    <!-- // Open the global edit modal -->
    <script>
        function openGlobalEditModal() {
            var modal = document.getElementById("globalEditModal");
            var selectedOrderIds = [];

            // Get all checked checkboxes and store their values (Order IDs)
            document.querySelectorAll('.order-checkbox:checked').forEach(function(checkbox) {
                selectedOrderIds.push(checkbox.value);
            });

            // Check if there are any selected orders
            if (selectedOrderIds.length === 0) {
                alert("Please select at least one order.");
                return;
            }

            // Set the hidden input value with the selected Order IDs
            document.getElementById("selectedOrderIds").value = selectedOrderIds.join(",");

            // Show the modal
            modal.style.display = "block";
        }

        // Close the modal
        function closeGlobalEditModal() {
            var modal = document.getElementById("globalEditModal");
            modal.style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("globalEditModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <!-- qr -->
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