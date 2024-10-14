<?php
include '../../connection.php';

// HEADER
$admin_id = $_SESSION['admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

$admin_username = htmlspecialchars($admin['Username'] ?? 'Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator');

// Initialize variables
$totalSales = 0.00;
$filteredTotalSales = 0.00;

$sqlTotal = "SELECT SUM(TotalAmount) AS TotalSales FROM orders WHERE OrderStatus = 'Delivered'";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalSales = $rowTotal['TotalSales'];

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

$sales_count = $resultSales->num_rows;
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 20px;
        }

        .sales-header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .total-sales h5 {
            font-size: 24px;
            color: #333;
        }

        .filter {
            text-align: left;
        }

        .filter select {
            padding: 8px;
            margin-right: 10px;
        }

        .filter button {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 50%;
        }

        .filter .print button {
            width: 100%;
        }

        .filter button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        table th {
            background-color: #16a085;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <section class="home">
        <?php include 'header.php'; ?>

        <div class="container-fluid">
            <div class="sales-header">
                <h1>Sales Report</h1>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <div class="total-sales">
                            <h5>Total Sales: ₱<?php echo number_format($totalSales, 2); ?></h5>
                            <h5>Filtered Total Sales: ₱<?php echo number_format($filteredTotalSales, 2); ?></h5>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4">
                        <div class="filter">
                            <form method="post" class="d-flex">
                                <label for="month">Filter by Month: &nbsp;</label>
                                <select name="month" id="month">
                                    <option value="">Select Month</option>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?php echo $i; ?>" <?php if (isset($month) && $month == $i) echo 'selected'; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="pt-2"><i class="fas fa-filter"></i> Filter</button>
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
                    <div class="sales-table">
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
                                <?php foreach ($salesData as $row) : ?>
                                    <tr>
                                        <td><?php echo $row_counter++; ?></td>
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
            </div>
        </div>
    </section>

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
</body>

</html>