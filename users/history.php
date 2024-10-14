<?php
session_start(); // Ensure session is started

include('../connection.php');

// Fetch user orders from the database
$customerID = $_SESSION['customer_id'];
$sql = "SELECT o.OrderID, o.OrderDate, p.ProductName, o.TotalAmount, o.OrderStatus
        FROM orders o
        INNER JOIN orderitems oi ON o.OrderID = oi.OrderID
        INNER JOIN products p ON oi.ProductID = p.ProductID
        WHERE o.CustomerID = ? 
        AND (o.OrderStatus = 'Delivered' OR o.OrderStatus = 'Cancelled')
        ORDER BY o.OrderDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .buy-container {
            margin: 80px auto;
        }

        /* 
        .buy-container th {
            font-size: 15px;
        } */

        .head {
            margin: auto;
            display: flex;
            justify-content: center;
            background-color: #fffefb;
            border: 1px solid rgba(224, 168, 0, .4);
            border-radius: 2px;
            border-radius: 3px;
            margin-bottom: 15px;
        }

        table thead tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table thead tr:hover {
            background-color: #000;
        }

        /* table thead tr th th {
            font-size: 15px;
        } */
        table thead tr th,
        table tbody tr td {
            font-size: 12px;
        }

        .header-container {
            background-color: #fffefb;
            border: 1px solid rgba(224, 168, 0, .4);
            border-radius: 2px;
            border-radius: 3px;
            box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            margin-bottom: 15px;
        }

        .header-container h5 {
            color: green;
            font-weight: 500;
            margin: 0;
        }

        /* Define colors for different status */
        .status-pending {
            color: orange;
        }

        .status-processing {
            color: blue;
        }

        .status-shipped {
            color: green;
        }

        .status-cancelled {
            color: red;
        }

        .status-delivered {
            color: purple;
        }
    </style>
    <style>
        .table-container {
            max-height: 500px;
            min-height: 500px;
            border-bottom: 2px solid lightblue;
            overflow-y: auto;
            scrollbar-width: thin;
        }
    </style>
    <style>
        /*@media only screen and (max-width: 600px) and (min-device-width: 320px) and (max-device-width: 768px) and (-webkit-min-device-pixel-ratio: 3) {*/
        @media (max-width: 510px) {

            table thead tr th,
            table tbody tr td {
                font-size: 10px;
                padding: 0;
                /* border: 1px solid #000; */
            }

            table tbody tr td.btns button {
                font-size: 10px;
                padding: 5px;
            }

            table tbody tr td img {
                width: 30px;
                height: 30px;
            }

            .header-container {
                padding: 0.35rem 0.50rem;
            }

            .header-container h5 {
                font-size: 15px;
            }

        }
    </style>
</head>

<body>
    <?php include 'navbars/navbar.php'; ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">History</li>
        </ol>
    </nav>
    <div class="buy-container vh-100">
        <div class="container">
            <div class="container header-container">
                <h5><i class="fa-solid fa-clock-rotate-left"></i> Order History</h5>
            </div>
            <!-- <div class="head">
                <h2 class="fw-bold p-1">Order History</h2>
            </div> -->
            <?php if (empty($orders)) : ?>
                <!-- Display message if no orders found -->
                <table class="table mt-1">
                    <tr>
                        <td>
                            <div class="text-center mt-3">
                                <img src='users/mr3.png' alt='No cancelled orders' style='width:300px; height:auto;'>
                                <h3>Orders is Empty.</h3>
                            </div>
                        </td>
                    </tr>
                </table>
            <?php else : ?>
                <!-- Display table if orders are found -->
                <div style="overflow-x:auto;" class="table-container col-12">
                    <table class="table mt-1">
                        <thead class="table-success">
                            <tr>
                                <!-- <th>Order No.</th> -->
                                <th>Order_Date</th>
                                <th>Name</th>
                                <th>Total_Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orderNumber = 1; // Initialize order number counter
                            foreach ($orders as $order) : ?>
                                <tr>
                                    <!-- <td><?php echo $orderNumber++; ?></td> -->
                                    <td><?php echo date("F j, Y", strtotime($order['OrderDate'])); ?></td>
                                    <td><?php echo $order['ProductName']; ?></td>
                                    <td class="text-danger">₱<?php echo $order['TotalAmount']; ?></td>
                                    <td class="btns">
                                        <?php
                                        switch ($order['OrderStatus']) {
                                            case 'Pending':
                                                echo '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" data-order-id="' . $order['OrderID'] . '">Cancel</button>';
                                                break;
                                            case 'Processing':
                                                echo '<button class="btn btn-primary btn-sm" disabled>Processing</button>';
                                                break;
                                            case 'Shipped':
                                                echo '<button class="btn btn-info btn-sm" disabled>Shipping</button>';
                                                break;
                                            case 'Delivered':
                                                echo '<button class="btn btn-success btn-sm" disabled>Delivered</button>';
                                                break;
                                            case 'Cancelled':
                                                echo '<button class="btn btn-secondary btn-sm" disabled>Cancelled</button>';
                                                break;
                                            default:
                                                echo '<button class="btn btn-secondary btn-sm" disabled>Unknown Status</button>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <!-- <tfoot>
                    <tr>
                        <td class="back-button" colspan="6">
                            <a href="dashboard.php"><i class='bx bx-arrow-back'></i> home</a>
                        </td>
                    </tr>
                </tfoot> -->
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>