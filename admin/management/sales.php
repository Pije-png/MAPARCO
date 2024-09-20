<?php
include '../../connection.php';

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
if (isset($_POST['month']) && !empty($_POST['month'])) {
    $month = $_POST['month'];
    $monthFilter = "AND MONTH(OrderDate) = $month";
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
    <title>Sales Report</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <style>
        /* Include the CSS styling here for simplicity */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2,
        h4 {
            text-align: center;
            color: #333;
        }

        .total-sales {
            margin-bottom: 20px;
        }

        .filter {
            margin-bottom: 20px;
            text-align: center;
        }

        .filter form {
            display: inline-block;
        }

        .filter label {
            margin-right: 10px;
        }

        .filter select {
            padding: 5px;
            margin-right: 10px;
        }

        .filter button {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter button:hover {
            background-color: #0056b3;
        }

        .sales-table table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .sales-table th {
            text-align: left;
            font-size: 12px;
            padding: 10px;
            background-color: #8fd19e;
        }

        .sales-table td {
            padding: 2px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
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

        .scrollsply {
            max-height: 380px;
            min-height: 380px;
            scrollbar-width: thin;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#print_btn').click(function() {
                var month = $('#month').val();
                var url = "print_sales.php";
                if (month) {
                    url += "?month=" + month;
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
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <section class="home bg bg-success">
        <div class="table container-fluid table container-fluid">
            <h2 class="fw-bold fs-3 pt-3">Sales Report</h2>

            <div class="total-sales">
                <h4>Total Sales: ₱<?php echo number_format($totalSales, 2); ?></h4>
                <h4>Filtered Total Sales: ₱<?php echo number_format($filteredTotalSales, 2); ?></h4>
            </div>

            <div class="filter">
                <form method="post">
                    <label for="month">Filter by Month:</label>
                    <select name="month" id="month">
                        <option value="">Select Month</option>
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php if (isset($month) && $month == $i) echo 'selected'; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit">Filter</button>
                </form>
                <button class="btn btn-success btn-sm btn-block col-md-2 float-right" type="button" id="print_btn">
                    <span class="fa fa-print"></span> Print
                </button>
            </div>

            <div class="sales-table scrollsply" style="overflow-x:auto;">
                <table>
                    <thead class="table-success">
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
                        <?php foreach ($salesData as $row) : ?>
                            <tr>
                                <td><?php echo $row['OrderID']; ?></td>
                                <td><?php echo $row['CustomerName']; ?></td>
                                <td><?php echo date("F j, Y", strtotime($row['OrderDate'])); ?></td>
                                <td class="text-danger">₱<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                <td><?php echo $row['OrderStatus']; ?></td>
                                <td><?php echo $row['ProductName']; ?></td>
                                <td><img src="<?php echo $row['Photo']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>

</html>