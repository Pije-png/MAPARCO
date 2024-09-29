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

        $global_update_message = "Orders updated successfully!";
    } else {
        $global_update_message = "Select a status to update!";
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

$paymentStatus = isset($_GET['paymentStatus']) ? $_GET['paymentStatus'] : 'All';

// Get current page number from the query parameter (default to 1 if not set)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Calculate the offset for the query
$offset = ($page - 1) * $rowCount;

// Add the LIMIT and OFFSET to your SQL query
$sql = "SELECT o.*, a.FullName AS CustomerName, a.Description, a.HouseNo, a.Street, a.Barangay, a.City, a.Province, a.ZipCode, 
        p.ProductName, oi.Quantity, oi.Subtotal 
        FROM orders o 
        JOIN addresses a ON o.AddressID = a.AddressID
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN products p ON oi.ProductID = p.ProductID
        WHERE o.OrderStatus = 'Pending'";

if ($paymentStatus !== 'All') {
    $paymentStatus = $conn->real_escape_string($paymentStatus);
    if ($paymentStatus === 'Not Paid') {
        $sql .= " AND o.PaymentStatus = 'Pending'";
    } else {
        $sql .= " AND o.PaymentStatus = '$paymentStatus'";
    }
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (o.OrderID LIKE '%$search%' 
                  OR a.FullName LIKE '%$search%' 
                  OR p.ProductName LIKE '%$search%')";
}

// Add sorting and limit for pagination
$sql .= " ORDER BY $sortField $sortOrder 
          LIMIT $rowCount OFFSET $offset";

$result = $conn->query($sql);

// Fetch total number of pending orders for pagination
$total_rows_sql = "SELECT COUNT(*) AS total_count 
                   FROM orders o 
                   WHERE o.OrderStatus = 'Pending'";
$total_rows_result = $conn->query($total_rows_sql);
$total_rows = $total_rows_result->fetch_assoc()['total_count'];

// Calculate total pages
$total_pages = ceil($total_rows / $rowCount);

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
    <link rel="stylesheet" href="css/orders.css">
    <title>Orders</title>
</head>

<body class="bg bg-light">

    <?php include 'sidebar-orders.php'; ?>

    <section class="home">
        <div class="order-container">
            <div class="container-fluid">
                <div class="pt-2 pb-5">
                    <div class="head pb-2">
                        <div class="arrow left"></div>
                        <p class="pending-header text-center h4 fw-bold text-light" style="font-style: italic; font-family: cursive; "><i class="fa-solid fa-fire"></i> List of Pending</p>
                        <div class="arrow right"></div>
                    </div>
                    <div class="text-white column pb-3 justify-content-center">
                        <div class="col-auto">
                            <div class="filter-option" data-filter="All">All</div>
                        </div>
                        <div class="col-auto">
                            <div class="filter-option" data-filter="Paid">Paid</div>
                        </div>
                        <div class="col-auto">
                            <div class="filter-option" data-filter="Not Paid">Not Paid</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="status-messages">
                                <?php if (isset($global_update_message)) {
                                    echo "<div class='status-message'>" . htmlspecialchars($global_update_message) . " <i class='bx bxs-check-circle text-success'></i></div>";
                                } ?>
                            </div>
                            <div class="orders-table-container">
                                <div class="header-container">
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
                                                <button class="btn btn-primary" style="font-size: 12px;" type="submit"><i class='bx bx-search'></i></button>
                                                <!-- Clear Button -->
                                                <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-outline-danger" style="margin-left: 3px; border-radius: 0px 5px 5px 0px">x</a>
                                            </div>
                                        </form>
                                    </div>

                                    <table class="admin-dashboard">
                                        <thead>
                                            <tr class="fw-bold fs-5 bg bg-success text-light">
                                                <!-- <th></th> -->
                                                <th colspan="9">Pending
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
                                                <th>Date Ordered</th>
                                                <th>Quantity</th>
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
                                                    echo "<td>" . htmlspecialchars($row["ProductName"]) . "</td>";
                                                    echo "<td>" . date("F j, Y", strtotime($row["OrderDate"])) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row["Quantity"]) . "</td>";
                                                    echo "<td class='TotalAmount text-danger'>₱" . htmlspecialchars($row["TotalAmount"]) . "</td>";
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
                                                echo "<tr><td colspan='10'>
                                                <img src='mr3.png' alt='No cancelled orders' style='width:300px; height:auto;'>
                                                <h3>No orders.</h3>
                                                </td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot class="bg bg-light">
                                            <td colspan="9">

                                            </td>
                                            <td colspan="2" class="text-center p-0 fs-6">
                                                <button type="button" id="clearCheckboxes" class="btn btn-outline-basic btn-sm">Clear</button>
                                            </td>
                                        </tfoot>

                                    </table>
                                    <?php include 'modal/modal_pending.php'; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pagination d-flex pt-2" style="justify-content:center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" style="margin: 0; padding: 0;">
                                <!-- Previous Button -->
                                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&rowCount=<?php echo $rowCount; ?>&sortBy=<?php echo $sortField; ?>&search=<?php echo $search; ?>" aria-label="Previous" style="padding: 0.25rem 1.5rem; font-size: 12px;">
                                        <span aria-hidden="true">
                                            &laquo; Previous
                                        </span>
                                    </a>
                                </li>
                                <!-- Page Number Links -->
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&rowCount=<?php echo $rowCount; ?>&sortBy=<?php echo $sortField; ?>&search=<?php echo $search; ?>" style="padding: 0.25rem 1.5rem; font-size: 12px;">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                <!-- Next Button -->
                                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&rowCount=<?php echo $rowCount; ?>&sortBy=<?php echo $sortField; ?>&search=<?php echo $search; ?>" aria-label="Next" style="padding: 0.25rem 1.5rem; font-size: 12px;">
                                        <span aria-hidden="true">
                                            Next &raquo;
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
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
    <script>
        document.querySelectorAll('.filter-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all filter options
                document.querySelectorAll('.filter-option').forEach(opt => {
                    opt.classList.remove('active');
                });

                // Add active class to the clicked option
                this.classList.add('active');

                // Get the filter and update the URL
                const filter = this.getAttribute('data-filter');
                const url = new URL(window.location.href);
                if (filter === 'All') {
                    url.searchParams.delete('paymentStatus');
                } else {
                    url.searchParams.set('paymentStatus', filter);
                }
                window.location.href = url.href;
            });
        });

        // Set the active filter based on the URL parameter
        window.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('paymentStatus') || 'All';

            // Set the active class based on the current filter in the URL
            document.querySelectorAll('.filter-option').forEach(option => {
                if (option.getAttribute('data-filter') === paymentStatus) {
                    option.classList.add('active');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.querySelector('.status-message');
            if (messageElement) {
                setTimeout(() => {
                    messageElement.classList.add('fade-out');
                }, 8000);
            }
        });
    </script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>