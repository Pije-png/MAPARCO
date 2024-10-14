<?php
include '../connection.php'; 

// Initialize variables
$totalSales = 0.00;
$filteredTotalSales = 0.00;

// Fetch total sales
$sqlTotal = "SELECT SUM(TotalAmount) AS TotalSales FROM orders WHERE OrderStatus = 'Delivered'";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalSales = $rowTotal['TotalSales'];

// Fetch sales data with optional monthly filter
$monthFilter = '';
$monthName = 'All Time';
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $month = $_GET['month'];
    $monthFilter = "AND MONTH(OrderDate) = $month";
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
}

$sqlSales = "SELECT o.OrderID, c.Name AS CustomerName, o.OrderDate, o.TotalAmount, o.OrderStatus, p.ProductName, p.Photo
             FROM orders o
             INNER JOIN customers c ON o.CustomerID = c.CustomerID
             INNER JOIN orderitems oi ON o.OrderID = oi.OrderID
             INNER JOIN products p ON oi.ProductID = p.ProductID
             WHERE o.OrderStatus = 'Delivered' $monthFilter
             ORDER BY o.OrderDate DESC";
$resultSales = $conn->query($sqlSales);

$filteredTotalSales = 0.00;
$salesData = [];

while ($row = $resultSales->fetch_assoc()) {
    $salesData[] = $row;
    $filteredTotalSales += $row['TotalAmount'];
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            padding: 20px;
            background-color: #fff;
        }

        h1, h2, h3 {
            text-align: center;
            color: #333;
        }

        .total-sales {
            margin-bottom: 20px;
        }

        .sales-table table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .sales-table th, .sales-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .sales-table th {
            background-color: #f4f4f4;
        }

        .sales-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sales-table tr:hover {
            background-color: #f1f1f1;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>MAPARCO's E-Commerce Sales</h1>
        <h2><?php echo $monthName; ?></h2>

        <div class="total-sales">
            <h3>Total Sales: $<?php echo number_format($totalSales, 2); ?></h3>
            <h3>Filtered Total Sales: $<?php echo number_format($filteredTotalSales, 2); ?></h3>
        </div>

        <div class="sales-table">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                        <th>Product Name</th>
                        <th>Product Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesData as $row): ?>
                        <tr>
                            <td><?php echo $row['OrderID']; ?></td>
                            <td><?php echo $row['CustomerName']; ?></td>
                            <td><?php echo $row['OrderDate']; ?></td>
                            <td>$<?php echo number_format($row['TotalAmount'], 2); ?></td>
                            <td><?php echo $row['OrderStatus']; ?></td>
                            <td><?php echo $row['ProductName']; ?></td>
                            <td><img src="management/<?php echo $row['Photo']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
