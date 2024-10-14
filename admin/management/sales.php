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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Revenue Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

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

        .revenue-header {
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

        .total-revenue h5 {
            font-size: 24px;
            color: #333;
        }

        .filter-controls option {
            font-size: 12px;
        }

        .filter-controls select {
            padding: 8px;
            margin-right: 10px;
        }

        .filter-controls button {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 50%;
        }

        .filter-controls .print button {
            width: 100%;
        }

        .filter-controls button:hover {
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

        .filter-controls label {
            font-size: 14px;
        }

        #report_month {
            font-size: 14px;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            font-size: 16px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-results__option {
            font-size: 16px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <section class="home">
        <?php include 'header.php'; ?>

        <div class="container-fluid pb-4">
            <div class="revenue-header">
                <h1>Revenue Report</h1>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <div class="total-revenue">
                            <h5>Total Revenue: ₱<?php echo number_format($totalRevenue, 2); ?></h5>
                            <h5>Filtered Total Revenue: ₱<?php echo number_format($filteredTotalRevenue, 2); ?></h5>
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

</body>

</html>