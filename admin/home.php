<?php
include('../connection.php');

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
$totalIncomeQuery = "SELECT SUM(TotalAmount) AS totalIncome FROM orders WHERE PaymentStatus = 'Paid' AND OrderDate > (NOW() - INTERVAL 30 DAY)";
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
    <link rel="stylesheet" href="css/styless.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tab-header {
            background-color: #f7f7f7;
            padding-top: 30px;
            margin-bottom: 20px;
        }

        .tab-header .nav-link {
            color: green;
        }

        .tab-header .nav-link:hover {
            color: green;
        }

        /* Override the left padding or margin for .home */
        .tab-header {
            padding-left: 20px !important;
            margin-left: 20px !important;
        }

        .margin-left {
            padding-left: 15px !important;
            margin-left: 15px !important;
        }

        /* .nav-home {
            padding-left: 20px !important;
            margin-left: 20px !important;
        } */
    </style>

</head>

<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'inventory.php'; ?>
    <section class="home">
        <div class="container-fluid">
            <!-- Tab Navigation Menu -->
            <div class="tab-header">
                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
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
                </ul>
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
                        <div class="container-fluid">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card text-white bg-success sm-3">
                                        <div class="card-body">
                                        <h6 class="card-title">Total Products Sold</h6>
                                            <h4 class="card-text"><?php echo $totalOrders; ?></h4>
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
                                <div class="col-md-4">
                                    <div class="card text-white bg-success sm-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Sales</h6>
                                            <h4 class="card-text">₱<?php echo number_format($totalIncome, 2); ?></h4>
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
                                            <th style="width:10%">Sold</th>
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
                                                echo "<td class='text-success'>" . $row['QuantitySold'] . "</td>";
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
                </div>
            </div>
        </div>
    </section>

    <?php include 'chart.php'; ?>

</body>

</html>