<?php
include '../../connection.php';

// Fetch all orders with customer names and product details
$sql = "SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.OrderStatus, c.Name AS CustomerName,
               p.ProductName, p.Photo
        FROM orders o
        JOIN customers c ON o.CustomerID = c.CustomerID
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN products p ON oi.ProductID = p.ProductID
        ORDER BY o.OrderDate DESC";

$result = $conn->query($sql);

// Get the number of products
$delivered_count = $result->num_rows;

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Order History</title>
</head>
<style>
    td img {
        max-width: 50px;
        height: auto;
        border-radius: 4px;
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
</style>
</head>

<body class="bg bg-light">

    <?php include 'sidebar.php'; ?>

     

    <section class="home">
        <div class="customer-container">
            <div class="container-fluid">
                <div class="head pt-3">
                    <h4 class="text-center">History List</h4>
                </div>
                <div class="orders-table-container">
                    <table class="admin-dashboard">
                        <thead>
                            <tr class="fw-bold fs-5 bg bg-success text-light">
                                <th colspan="7">History List
                                    <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $delivered_count; ?></span>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <!-- <th>CustomerID</th> -->
                                <th style="width:2%"></th>
                                <th>Photo</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Order Date</th>
                                <th>Order Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg bg-light text-center">
                            <?php
                            $row_counter = 1; // Initialize row_counter

                            while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $row_counter++; ?></td> <!-- Proper echo syntax for row_counter -->
                                    <td><img src="<?php echo $row['Photo']; ?>" alt="<?php echo $row['ProductName']; ?>" class="product-image"></td>
                                    <td><?php echo $row['ProductName']; ?></td>
                                    <td><?php echo $row['CustomerName']; ?></td>
                                    <td class="text-danger">₱<?php echo $row['TotalAmount']; ?></td>
                                    <td><?php echo date("F j, Y", strtotime($row['OrderDate'])); ?></td>
                                    <td>
                                        <?php
                                        switch ($row['OrderStatus']) {
                                            case 'Pending':
                                                echo '<span class="bg-danger text-light p-1 rounded">Pending</span>';
                                                break;
                                            case 'Processing':
                                                echo '<span class="bg-primary text-light p-1 rounded">Processing</span>';
                                                break;
                                            case 'Shipped':
                                                echo '<span class="bg-warning text-light p-1 rounded">Shipping</span>';
                                                break;
                                            case 'Delivered':
                                                echo '<span class="bg-success text-light p-1 rounded">Delivered</span>';
                                                break;
                                            case 'Cancelled':
                                                echo '<span class="bg-secondary text-light p-1 rounded">Cancelled</span>';
                                                break;
                                            default:
                                                echo '<span class="bg-secondary text-light p-1 rounded">Unknown Status</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </section>
</body>

</html>