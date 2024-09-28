<?php
include '../../../connection.php';

if (isset($_POST['update_global_status'])) {
    $orderIDs = $_POST['selected_order_ids'];
    $orderIDsArray = explode(',', $orderIDs);

    $newOrderStatus = $_POST['new_global_order_status'] ?? null;
    $newPaymentStatus = $_POST['new_global_payment_status'] ?? null;

    if ($newOrderStatus || $newPaymentStatus) {
        foreach ($orderIDsArray as $orderID) {
            if ($newOrderStatus) {
                $stmt = $conn->prepare("UPDATE orders SET OrderStatus = ? WHERE OrderID = ?");
                $stmt->bind_param("si", $newOrderStatus, $orderID);
                $stmt->execute();
            }

            if ($newPaymentStatus) {
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

$rowCount = isset($_GET['rowCount']) ? (int)$_GET['rowCount'] : 10;
$allowedRowCounts = [5, 10, 25, 50, 100];
if (!in_array($rowCount, $allowedRowCounts)) {
    $rowCount = 10;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Sorting logic
$sortField = 'o.OrderDate';  // Default sort by date
$sortOrder = "DESC";  // Default to "Newest"

if (isset($_GET['sortBy'])) {
    switch ($_GET['sortBy']) {
        case 'name':
            $sortField = 'a.FullName';
            $sortOrder = 'ASC';
            break;
        case 'newest':
            $sortField = 'o.OrderDate';
            $sortOrder = 'DESC';
            break;
        case 'oldest':
            $sortField = 'o.OrderDate';
            $sortOrder = 'ASC';
            break;
    }
}

$sql = "SELECT o.*, a.FullName AS CustomerName, a.Description, a.HouseNo, a.Street, a.Barangay, a.City, a.Province, a.ZipCode, 
        p.ProductName, oi.Quantity, oi.Subtotal 
        FROM orders o 
        JOIN addresses a ON o.AddressID = a.AddressID
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN products p ON oi.ProductID = p.ProductID
        WHERE o.OrderStatus = 'Pending'";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (o.OrderID LIKE '%$search%' 
                  OR a.FullName LIKE '%$search%' 
                  OR p.ProductName LIKE '%$search%')";
}

$sql .= " ORDER BY $sortField $sortOrder
          LIMIT $rowCount";

$result = $conn->query($sql);
$pending_count_sql = "SELECT COUNT(*) AS pending_count FROM orders WHERE OrderStatus = 'Pending'";
$pending_count_result = $conn->query($pending_count_sql);
$pending_count = $pending_count_result->fetch_assoc()['pending_count'];

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
        .container-fluid {
            background: linear-gradient(to bottom, MediumSeaGreen, white);
        }

        .admin-dashboard {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 0; */
        }

        table {
            border-collapse: collapse;

        }

        table tr,
        table th {
            font-size: 12px;
            border: 1px solid #999;
            padding: 5px;
        }

        table td {
            font-size: 12px;
            border: 1px solid #999;
            padding: 5px;
        }

        thead {
            background-color: #98FB98;
        }

        .payment-status-pending {
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

        i .qr {
            font-size: 10px;
        }

        /* Optional: Adding responsiveness */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 90%;
                /* Adjust modal width for smaller screens */
            }
        }
    </style>
    <style>
        .row-selection {
            display: flex;
            justify-content: space-between;
            /* background-color: #90EE90; */
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .row-selection label {
            font-size: 13px;
        }

        #rowCount {
            padding: 2px;
            font-size: 13px;
        }
    </style>
    <style>
        .input-group .form-control {
            border-radius: 5px 0 0 5px;
            box-shadow: none;
        }

        .form-control::placeholder {
            font-size: 12px;
        }

        .input-group .btn {
            border-radius: 0 5px 5px 0;
        }

        .input-group .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);

        }

        #clearSearch {
            margin-left: 10px;
            border-radius: 5px;
        }

        .column {
            display: flex;
            justify-items: start;
            gap: 5px;
        }
    </style>

</head>

<body class="bg bg-light">

    <?php include 'sidebar-orders.php'; ?>

    <section class="home">
        <div class="order-container">
            <div class="container-fluid">
                <div class="head pt-5">
                    <h4 class="text-center">List of Pending</h4>
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
                        <div class="row-selection p-1">
                            <div class="column">
                                <form method="get" action="">
                                    <label for="rowCount">Number of rows:</label>
                                    <select id="rowCount" name="rowCount" onchange="this.form.submit()">
                                        <option value="5" <?php if (isset($_GET['rowCount']) && $_GET['rowCount'] == '5') echo 'selected'; ?>>5</option>
                                        <option value="10" <?php if (!isset($_GET['rowCount']) || $_GET['rowCount'] == '10') echo 'selected'; ?>>10</option>
                                        <option value="25" <?php if (isset($_GET['rowCount']) && $_GET['rowCount'] == '25') echo 'selected'; ?>>25</option>
                                        <option value="50" <?php if (isset($_GET['rowCount']) && $_GET['rowCount'] == '50') echo 'selected'; ?>>50</option>
                                        <option value="100" <?php if (isset($_GET['rowCount']) && $_GET['rowCount'] == '100') echo 'selected'; ?>>100</option>
                                    </select>
                                </form>
                                <form method="get" action="">
                                    <div class="input-group">
                                        <select name="sortBy" style="font-size: 13px; padding: 2px" onchange="this.form.submit()">
                                            <option value="newest" <?php if (!isset($_GET['sortBy']) || $_GET['sortBy'] == 'newest') echo 'selected'; ?>>Newest</option>
                                            <option value="oldest" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'oldest') echo 'selected'; ?>>Oldest</option>
                                            <option value="name" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'name') echo 'selected'; ?>>By Name</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <!-- Search Form -->
                            <form method="get" action="" class="d-flex justify-content-center">
                                <div class="input-group" style="width: 380px;">
                                    <input type="text" class="form-control border border-success" name="search" placeholder="Search by Order ID, Name, or Product"
                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-primary" style="font-size: 12px;" type="submit">Search</button>
                                    <!-- Clear Button -->
                                    <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-outline-secondary" style="margin-left: 3px; border-radius: 0px 5px 5px 0px">x</a>
                                </div>
                            </form>
                        </div>

                        <table class="admin-dashboard">
                            <thead>
                                <tr class="fw-bold fs-5 bg bg-success text-light">
                                    <!-- <th></th> -->
                                    <th colspan="8">Pending
                                        <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo htmlspecialchars($pending_count); ?></span>
                                    </th>

                                    <th colspan="2">
                                        <!-- Global Edit Button -->
                                        <button type="button" class="editbtn btn btn-sm btn-success border-0" onclick="openGlobalEditModal()">Edit</button>
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th style="width:3%"></th>
                                    <th style="width:2%">Order&nbsp;ID</th>
                                    <th>Name</th>
                                    <th>Product</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <!-- <th>Shipping Address</th> -->
                                    <th>QR</th>
                                    <th style="width:3%">
                                        <input type="checkbox" id="selectAllCheckbox" style="transform: scale(1.2);">
                                    </th>
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

                                        echo "<td style='width: 0px'>";
                                        echo "<button type='button' class='btn btn-primary btn-sm' style='font-size:10px;' onclick='generateQRCode(" . htmlspecialchars($row["OrderID"]) . ", \"" . htmlspecialchars($row["HouseNo"] . " " . $row["Street"] . ", " . $row["Barangay"] . ", " . $row["City"] . ", " . $row["Province"] . " " . $row["ZipCode"]) . "\")'>
                                        <i class='bx bxs-download'></i>
                                      </button>";

                                        echo "</td>";
                                        echo "<td><input type='checkbox' name='order_ids[]' value='" . htmlspecialchars($row["OrderID"]) . "' class='order-checkbox' style='transform: scale(1.5);'></td>";
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
                        <?php include 'modal_pending.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

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