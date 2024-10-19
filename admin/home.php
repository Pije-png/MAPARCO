<?php
include('../connection.php');

function redirectToLogin()
{
    header("Location: ../login.php");
    exit;
}

// Perform the logout operation when a request is made to log out
if (isset($_POST['confirmLogout'])) {
    if (isset($_SESSION['super_admin_id'])) {
        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session for the super admin
        session_destroy();
        // Redirect to login page
        redirectToLogin();
    } elseif (isset($_SESSION['admin_id'])) {
        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session for the admin
        session_destroy();
        // Redirect to login page
        redirectToLogin();
    } elseif (isset($_SESSION['customer_id'])) {
        // Unset all of the session variables
        $_SESSION = array();
        // Destroy the session for the customer
        session_destroy();
        // Redirect to login page
        redirectToLogin();
    }
}

// Capture start and end date from the form (default to January 1st of the current year to today if not set)
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-01-01');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// Extract year from the start or end date
$startYear = date('Y', strtotime($startDate));
$endYear = date('Y', strtotime($endDate));

// Determine the year to display
$displayYear = ($startYear === $endYear) ? $startYear : "$startYear - $endYear";

// Updated queries to filter by selected date range
$newOrdersQuery = "SELECT COUNT(*) AS newOrders FROM orders WHERE OrderStatus = 'Pending' AND OrderDate BETWEEN '$startDate' AND '$endDate'";
$totalIncomeQuery = "SELECT SUM(TotalAmount) AS totalIncome FROM orders WHERE PaymentStatus = 'Paid' AND OrderDate BETWEEN '$startDate' AND '$endDate'";
$totalOrdersQuery = "SELECT COUNT(OrderID) AS totalOrders FROM orders WHERE OrderStatus != 'Cancelled' AND OrderDate BETWEEN '$startDate' AND '$endDate'";
$newUsersQuery = "SELECT COUNT(*) AS newUsers FROM customers WHERE create_on BETWEEN '$startDate' AND '$endDate'";

// Fetch data for dashboard metrics
$newOrders = $conn->query($newOrdersQuery)->fetch_assoc()['newOrders'];
$totalIncome = $conn->query($totalIncomeQuery)->fetch_assoc()['totalIncome'];
$totalOrders = $conn->query($totalOrdersQuery)->fetch_assoc()['totalOrders'];
$newUsers = $conn->query($newUsersQuery)->fetch_assoc()['newUsers'];

// Updated query for new orders as "Pending" only, without date interval
$newOrdersQuery = "SELECT COUNT(*) AS newOrders FROM orders WHERE OrderStatus = 'Pending'";

// Queries for other metrics
// $totalIncomeQuery = "SELECT SUM(TotalAmount) AS totalIncome FROM orders WHERE OrderDate > (NOW() - INTERVAL 30 DAY)";
$totalIncomeQuery = "SELECT SUM(TotalAmount) AS totalIncome FROM orders WHERE PaymentStatus = 'Paid' AND OrderDate";
$totalExpenseQuery = "SELECT SUM(Price * Quantity) AS totalExpense FROM orderitems WHERE OrderID IN (SELECT OrderID FROM orders WHERE OrderDate > (NOW() - INTERVAL 30 DAY))";

$newUsersQuery = "SELECT COUNT(*) AS newUsers FROM customers WHERE create_on > (NOW() - INTERVAL 30 DAY)";

// Fetch data for dashboard 
$newOrders = $conn->query($newOrdersQuery)->fetch_assoc()['newOrders'];
$totalIncome = $conn->query($totalIncomeQuery)->fetch_assoc()['totalIncome'];
$totalExpense = $conn->query($totalExpenseQuery)->fetch_assoc()['totalExpense'];
$newUsers = $conn->query($newUsersQuery)->fetch_assoc()['newUsers'];

// Queries for charts and top-selling products (unchanged)
$topSellingProductsQuery = "
    SELECT 
        p.ProductName, p.Price, p.Photo, 
        COALESCE(AVG(r.Rating), 0) AS AverageRating, 
        SUM(o.Quantity) AS Sales 
    FROM orderitems o 
    JOIN orders ord ON o.OrderID = ord.OrderID
    JOIN products p ON o.ProductID = p.ProductID 
    LEFT JOIN reviews r ON r.ProductID = p.ProductID 
    WHERE ord.PaymentStatus = 'Paid'
    GROUP BY o.ProductID 
    ORDER BY Sales DESC 
    LIMIT 5
";

$topSellingProductsResult = $conn->query($topSellingProductsQuery);

// Query for all top-selling products
$allTopSellingProductsQuery = "
    SELECT 
        p.ProductName, p.Price, p.Photo, 
        COALESCE(AVG(r.Rating), 0) AS AverageRating, 
        SUM(o.Quantity) AS Sales 
    FROM orderitems o 
    JOIN orders ord ON o.OrderID = ord.OrderID
    JOIN products p ON o.ProductID = p.ProductID 
    LEFT JOIN reviews r ON r.ProductID = p.ProductID 
    WHERE ord.PaymentStatus = 'Paid'
    GROUP BY o.ProductID 
    ORDER BY Sales DESC
";
$allTopSellingProductsResult = $conn->query($allTopSellingProductsQuery);

// Queries for charts
$salesByMonthQuery = "
    SELECT 
        MONTH(OrderDate) AS Month,
        SUM(TotalAmount) AS TotalSales
    FROM orders
    WHERE PaymentStatus = 'Paid' AND OrderDate BETWEEN '$startDate' AND '$endDate'
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate)
";

$salesByMonthResult = $conn->query($salesByMonthQuery);
$salesData = array_fill(1, 12, 0);

while ($row = $salesByMonthResult->fetch_assoc()) {
    $salesData[(int)$row['Month']] = (float)$row['TotalSales'];
}

$ordersByMonthQuery = "
    SELECT 
        MONTH(OrderDate) AS Month,
        COUNT(OrderID) AS TotalOrders
    FROM orders
    WHERE OrderDate BETWEEN '$startDate' AND '$endDate'
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate)
";

$ordersByMonthResult = $conn->query($ordersByMonthQuery);
$ordersData = array_fill(1, 12, 0);

while ($row = $ordersByMonthResult->fetch_assoc()) {
    $ordersData[(int)$row['Month']] = (int)$row['TotalOrders'];
}

// Fetch order counts by status
// Fetch order counts by status within the selected date range
$orderStatusQuery = "
    SELECT 
        OrderStatus, 
        COUNT(OrderID) AS StatusCount 
    FROM orders 
    WHERE OrderDate BETWEEN '$startDate' AND '$endDate'
    GROUP BY OrderStatus
";

$orderStatusResult = $conn->query($orderStatusQuery);

// Prepare the data for the chart
$orderStatusData = [
    'Pending' => 0,
    'Processing' => 0,
    'Shipped' => 0,
    'Delivered' => 0
];

// Populate data with actual counts
while ($row = $orderStatusResult->fetch_assoc()) {
    // Ensure that the order status exists in the array to avoid undefined index errors
    if (array_key_exists($row['OrderStatus'], $orderStatusData)) {
        $orderStatusData[$row['OrderStatus']] = (int)$row['StatusCount'];
    }
}

// SQL query to count total products
$sql = "SELECT COUNT(*) AS totalProducts FROM products";
$result = $conn->query($sql);

// Fetch the result
$row = $result->fetch_assoc();
$totalProducts = $row['totalProducts'];


// Initialize variables
$totalRevenue = 0.00;
$filteredTotalRevenue = 0.00;

$sqlRevenueTotal = "SELECT SUM(TotalAmount) AS TotalRevenue FROM orders WHERE OrderStatus = 'Delivered'";
$resultRevenueTotal = $conn->query($sqlRevenueTotal);
$rowRevenueTotal = $resultRevenueTotal->fetch_assoc();
$totalRevenue = $rowRevenueTotal['TotalRevenue'];

$dateFilter = '';
if (isset($_POST['report_month']) && !empty($_POST['report_month'])) {
    $report_month = $_POST['report_month'];
    $dateFilter = "AND MONTH(OrderDate) = $report_month";
}

$sqlRevenueDetails = "SELECT o.OrderID, c.Name AS CustomerName, o.OrderDate, o.TotalAmount, o.OrderStatus, p.ProductName, p.Photo
                      FROM orders o
                      INNER JOIN customers c ON o.CustomerID = c.CustomerID
                      INNER JOIN orderitems oi ON o.OrderID = oi.OrderID
                      INNER JOIN products p ON oi.ProductID = p.ProductID
                      WHERE o.OrderStatus = 'Delivered' $dateFilter
                      ORDER BY o.OrderDate DESC";
$resultRevenueDetails = $conn->query($sqlRevenueDetails);

$filteredTotalRevenue = 0.00;
$revenueData = [];

while ($row = $resultRevenueDetails->fetch_assoc()) {
    $revenueData[] = $row;
    $filteredTotalRevenue += $row['TotalAmount'];
}

$revenue_count = $resultRevenueDetails->num_rows;

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Get the number of products
$product_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/home4.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'inventory.php'; ?>
    <section class="home">
        <?php include 'header.php'; ?>
        <div class="container-fluid">
            <!-- Tab Navigation Menu -->
            <div class="tab-header">
                <ul class="nav nav-tabs d-flex justify-content-between w-100" id="dashboardTabs" role="tablist">
                    <div class="d-flex">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                                <h5>Dashboard</h5>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="false">
                                <h5>Inventory</h5>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab" aria-controls="sales" aria-selected="false">
                                <h5>Sales</h5>
                            </button>
                        </li>
                    </div>
                    <div class="nav-item pe-2" role="presentation">
                        <button class="nav-link border border-danger rounded pt-2 pb-0" id="logout-tab" type="button">
                            <h6><i class='bx bx-log-out icon'></i> Logout</h6>
                        </button>
                    </div>
                </ul>
            </div>

            <!-- Logout Confirmation Card -->
            <div class="confirmation-dialog" id="logoutConfirmationCard">
                <p class=" fw-bold fs-6">Log out?</p>
                <div class="rel">
                    <h6>Are you sure you want to log out?</h6>
                    <form id="logoutForm" method="POST">
                        <button type="button" id="confirmLogout" class="btn btn-danger btn btn-sm">Yes</button>
                        <button type="button" id="cancelLogout" class="btn btn-outline-secondary btn-sm">No</button>
                        <input type="hidden" name="confirmLogout" value="1">
                    </form>
                </div>
            </div>
            <div class="margin-left">
                <!-- Tab Content -->
                <div class="tab-content" id="dashboardTabContent">
                    <!-- Dashboard Tab Pane -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                        <!-- Dashboard Metric Cards -->
                        <div class="row">
                            <!-- Same Dashboard content as provided before -->
                            <div class="col-md-3">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">New Orders</h5>
                                        <p class="card-text"><?php echo $newOrders; ?> Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-danger mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Orders</h5>
                                        <p class="card-text"><?php echo $totalOrders; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Income</h5>
                                        <p class="card-text">₱<?php echo number_format($totalIncome, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">New Users</h5>
                                        <p class="card-text"><?php echo $newUsers; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Date Range Filter -->
                        <div class="card mb-4">
                            <div class="card-body py-3 border-left">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Label for Summary -->
                                    <label for="summaryChart" class="form-label fw-bold">Summary</label>

                                    <!-- Button in Text-Right -->
                                    <div class="text-end">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                                            <i class='bx bxs-calendar'></i> <?php echo $displayYear; ?> <i class='bx bxs-down-arrow'></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="filterModalLabel">
                                                    <i class='bx bxs-calendar'></i> <?php echo $displayYear; ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Date Range Filter Form Inside Modal -->
                                                <form method="GET" action="">
                                                    <div class="row mb-3 filter-modal">
                                                        <div class="col-md-6">
                                                            <label for="startDate" class="form-label">Start: </label>
                                                            <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo $startDate; ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="endDate" class="form-label">End: </label>
                                                            <input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo $endDate; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Row -->
                                <div class="row mx-1 my-0 mt-1">
                                    <div class="col-md-6 border rounded p-1">
                                        <canvas id="summaryChart"></canvas>
                                    </div>
                                    <div class="col-md-6 border rounded p-1 pie-chart">
                                        <canvas id="ordersPieChart" width="280" height="280"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Selling Products Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card text-black mb-5">
                                    <div class="card-body border-left">
                                        <h5 class="card-title">Top Selling Products</h5>
                                        <ul class="list-group">
                                            <?php
                                            $rank = 1;
                                            while ($row = $topSellingProductsResult->fetch_assoc()) {
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $rank === 1 ? 'highlight' : ''; ?>" style="<?php echo $rank === 1 ? 'background-color: #FFD700;' : ''; ?>">
                                                    <div class="product-details d-flex align-items-center">
                                                        <span class="rank-badge" style="background-color: <?php echo $rank === 1 ? '#FFD700' : ($rank === 2 ? '#C0C0C0' : '#CD7F32'); ?>; color: white; padding: 5px 10px; border-radius: 45%; margin-right: 15px;">
                                                            <?php echo $rank; ?>
                                                        </span>
                                                        <img src="management/<?php echo $row['Photo']; ?>" class="product-image" alt="error">
                                                        <div>
                                                            <strong>
                                                                <?php echo $row['ProductName']; ?>
                                                                <?php if ($rank === 1) : ?>
                                                                    <i class="fas fa-crown crown" style="font-size: 30px; color: gold; margin-left: 10px;"></i>
                                                                <?php endif; ?>
                                                            </strong>
                                                            <div class="rating">
                                                                <?php
                                                                $averageRating = round($row['AverageRating']);
                                                                for ($i = 1; $i <= 5; $i++) {
                                                                    echo $i <= $averageRating ? '<i class="fas fa-star" style="color: gold;"></i>' : '<i class="far fa-star" style="color: grey;"></i>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <span class="badge rounded-pill text-bg-primary"><?php echo $row['Sales']; ?> Sold</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge rounded-pill text-bg-secondary">₱<?php echo number_format($row['Price'], 2); ?></span>
                                                </li>
                                            <?php
                                                $rank++;
                                            }
                                            ?>
                                        </ul>
                                        <!-- See More Button -->
                                        <div class="text-center">
                                            <button type="button" class="btn btn-primary btn-md mt-3 px-4" data-bs-toggle="modal" data-bs-target="#topSellingModal">See More</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Tab Pane -->
                    <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
                        <div class="container-fluid pb-4">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card text-white bg-success sm-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Sold</h6>
                                            <h4 class="card-text"><?php echo $totalOrders; ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card text-white bg-success sm-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Sales</h6>
                                            <h4 class="card-text">₱<?php echo number_format($totalIncome, 2); ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card text-white bg-success sm-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Products</h6>
                                            <h4 class="card-text"><?php echo $totalProducts; ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="orders-table-container mb-5">
                                <table class="admin-dashboard">
                                    <thead>
                                        <tr class="fw-bold fs-5 bg bg-success text-light">
                                            <th colspan="9">Product List
                                                <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $product_count; ?></span>
                                            </th>
                                            <!-- <th colspan="2" class="text-center">
                                                <button type="button" class="editbtn btn btn-sm btn-success border-0" onclick="openCreateModal()">+ Add</button>
                                            </th> -->
                                        </tr>
                                        <tr class="text-center">
                                            <th style="width:2%"></th>
                                            <th style="width:0">Photo</th>
                                            <th style="width:25%">Product</th>
                                            <th style="width:25%">Description</th>
                                            <th style="width:10%">Price</th>
                                            <th style="width:10%">Stocks</th>
                                            <!-- <th style="width:10%">Sold</th> -->
                                            <th colspan="2" style="width:5%">Tools</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg bg-light text-center">
                                        <?php
                                        $row_counter = 1; // Initialize row_counter

                                        // Modified query to include QuantitySold
                                        $sql = "SELECT p.ProductID, p.ProductName, p.Photo, p.Description, p.Price, p.QuantityAvailable, 
                                          IFNULL(SUM(oi.Quantity), 0) AS QuantitySold
                                          FROM products p
                                          LEFT JOIN orderitems oi ON p.ProductID = oi.ProductID
                                          GROUP BY p.ProductID";

                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            // Output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row_counter++ . "</td>"; // Increment row_counter
                                                echo "<td><img src='management/" . $row['Photo'] . "' alt='Product Image' style='width:50px;height:50px;'></td>";
                                                echo "<td>" . $row["ProductName"] . "</td>";
                                                echo "<td>" . $row["Description"] . "</td>";
                                                echo "<td style='color: red; text-align: center;'> ₱" . $row["Price"] . "</td>";
                                                echo "<td style='color: blue; text-align: center'>" . $row["QuantityAvailable"] . "</td>";
                                                // echo "<td class='text-success'>" . $row['QuantitySold'] . "</td>";
                                                echo "<td>";
                                                // Open modal with product data when update link is clicked
                                                echo "<a href='#' onclick='openModal(" . $row["ProductID"] . ", \"" . $row["ProductName"] . "\", \"" . $row["Description"] . "\", " . $row["Price"] . ", " . $row["QuantityAvailable"] . ")' class='update-link mx-2'><i class='bx bxs-edit'></i></a>";
                                                // Open modal with delete confirmation dialog when delete link is clicked
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a href='#' onclick='openDeleteModal(" . $row["ProductID"] . ")' class='delete-link mx-2'><i class='bx bxs-trash'></i></a>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No products found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                        <div class="container-fluid pb-4 sales-tab">
                            <div class="revenue-header">
                                <h1>Sales Report</h1>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-8">
                                    <div class="card p-4">
                                        <div class="total-revenue">
                                            <h5>Total Sales: ₱<?php echo number_format($totalRevenue, 2); ?></h5>
                                            <h5>Filtered Total Sales: ₱<?php echo number_format($filteredTotalRevenue, 2); ?></h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card p-4">
                                        <div class="filter-controls">
                                            <form method="post" class="d-flex">
                                                <label for="report_month">Filter by Month: &nbsp;</label>
                                                <select name="report_month" id="report_month">
                                                    <option value="">Select Month</option>
                                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                        <option value="<?php echo $i; ?>" <?php if (isset($report_month) && $report_month == $i) echo 'selected'; ?>>
                                                            <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                                <button type="submit" class="pt-2"><i class="fas fa-filter"></i> Apply</button>
                                            </form>
                                            <p class="print">
                                                <button class="btn btn-sm btn-success mt-3" id="print_btn"><i class="fas fa-print"></i> Print</button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="revenue-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Order ID</th>
                                                    <th>Customer Name</th>
                                                    <th>Order Date</th>
                                                    <th>Total Amount</th>
                                                    <th>Order Status</th>
                                                    <th>Product</th>
                                                    <th>Photo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $row_counter = 1; ?>
                                                <?php foreach ($revenueData as $row) : ?>
                                                    <tr>
                                                        <td><?php echo $row_counter++; ?></td>
                                                        <td><?php echo $row['OrderID']; ?></td>
                                                        <td><?php echo $row['CustomerName']; ?></td>
                                                        <td><?php echo date("F j, Y", strtotime($row['OrderDate'])); ?></td>
                                                        <td class="text-danger">₱<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                                        <td><?php echo $row['OrderStatus']; ?></td>
                                                        <td><?php echo $row['ProductName']; ?></td>
                                                        <td><img src="management/<?php echo $row['Photo']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'chart.php'; ?>
    <script>
        // Ensure DOM is fully loaded before running the script
        document.addEventListener("DOMContentLoaded", function() {
            // Show the confirmation card when the logout button is clicked
            document.getElementById("logout-tab").addEventListener("click", function() {
                document.getElementById("logoutConfirmationCard").style.display = "block";
            });

            // Hide the confirmation card if "Cancel" is clicked
            document.getElementById("cancelLogout").addEventListener("click", function() {
                document.getElementById("logoutConfirmationCard").style.display = "none";
            });

            // When the "Yes" button is clicked, submit the form to log out
            document.getElementById("confirmLogout").addEventListener("click", function() {
                document.getElementById("logoutForm").submit();
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#print_btn').click(function() {
                var report_month = $('#report_month').val();
                var url = "print_sales.php";
                if (report_month) {
                    url += "?report_month=" + report_month;
                }
                var nw = window.open(url, "_blank", "height=500,width=800");
                setTimeout(function() {
                    nw.print();
                    setTimeout(function() {
                        nw.close();
                    }, 500);
                }, 1000);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#report_month').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
    <style>
        .sales-tab .revenue-header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .sales-tab .card {
            /* border: none; */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .sales-tab .total-revenue h5 {
            font-size: 24px;
            color: #333;
        }

        .sales-tab .filter-controls option {
            font-size: 12px;
        }

        .sales-tab .filter-controls select {
            padding: 8px;
            margin-right: 10px;
        }

        .sales-tab .filter-controls button {
            padding: 8px 8px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 50%;
        }

        .sales-tab .filter-controls .print button {
            width: 100%;
        }

        .sales-tab .filter-controls button:hover {
            background-color: #0056b3;
        }

        .sales-tab table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sales-tab table th,
        .sales-tab table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .sales-tab table th {
            background-color: #16a085;
            color: white;
        }

        .sales-tab table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sales-tab table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .sales-tab .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .sales-tab .filter-controls label {
            font-size: 14px;
        }

        .sales-tab .sales-tab #report_month {
            font-size: 14px;
        }

        .sales-tab .select2-container .select2-selection--single {
            height: 38px;
            font-size: 16px;
        }

        .sales-tab .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .sales-tab .select2-results__option {
            font-size: 16px;
        }
    </style>
</body>

</html>