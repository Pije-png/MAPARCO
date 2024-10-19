<?php
include '../../connection.php';

// Check if super admin is logged in
if (!isset($_SESSION['super_admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Fetch super admin details
$super_admin_id = $_SESSION['super_admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ? AND Is_SuperAdmin = 1");
$query->bind_param("i", $super_admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Set default values in case data is missing
$admin_username = htmlspecialchars($admin['Username'] ?? 'Super Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Super Administrator');

// Fetch activity logs
$sql = "SELECT a.Full_Name, al.action, al.timestamp, al.product_id, p.ProductName, al.OldValue, al.NewValue
FROM activity_log al 
JOIN admins a ON al.admin_id = a.ID 
LEFT JOIN products p ON al.product_id = p.ProductID 
ORDER BY al.timestamp DESC";

$result_logs = $conn->query($sql);

// Fetch order status history logs
$sql_order_logs = "SELECT a.Full_Name, osh.OldOrderStatus, osh.NewOrderStatus, osh.OldPaymentStatus, 
                          osh.NewPaymentStatus, osh.UpdateTimestamp, osh.OrderID
        FROM order_status_history osh
        JOIN admins a ON osh.UpdatedBy = a.ID
        ORDER BY osh.UpdateTimestamp DESC";

$result_order_logs = $conn->query($sql_order_logs);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Activity Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        /* Add this to the existing CSS in the <style> block */

        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            max-width: 200px;
            /* Set a maximum width for the cells */
            word-wrap: break-word;
            /* Break words that are too long */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Add ellipsis for long text */
            white-space: nowrap;
            /* Prevent wrapping by default */
        }

        td:hover {
            white-space: normal;
            /* Allow wrapping on hover */
            overflow: visible;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .admin-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .admin-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }
    </style>
</head>

<body>
    <section>
        <header>
            <h1>Activity Log</h1>
        </header>
        <div>
            <a href="../home.php">Back to dashboard</a>
        </div>
        <div class="container">
            <div class="admin-info">
                <img src="<?php echo $admin_photo; ?>" alt="Admin Photo">
                <div>
                    <h2><?php echo $admin_full_name; ?></h2>
                    <p><?php echo $admin_username; ?></p>
                </div>
            </div>

            <h2>Product Actions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Product ID</th>
                        <th>Product</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $result_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['Full_Name']); ?></td>
                            <td>
                                <?php
                                $action = htmlspecialchars($log['action']);
                                if (in_array($action, ['Deleted', 'Updated', 'Added'])) {
                                    echo $action;
                                } else {
                                    echo 'Deleted';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($log['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['ProductName'] ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                $oldValue = json_decode($log['OldValue'], true);
                                $newValue = json_decode($log['NewValue'], true);
                                $changesExist = false;

                                if ($oldValue && $newValue) {
                                    $output = '';

                                    // Check and display only the fields that have changed
                                    if (($oldValue['ProductName'] ?? '') !== ($newValue['ProductName'] ?? '')) {
                                        $output .= "ProductName: " . htmlspecialchars($oldValue['ProductName'] ?? 'N/A') . "<br>";
                                        $changesExist = true;
                                    }
                                    if (($oldValue['Description'] ?? '') !== ($newValue['Description'] ?? '')) {
                                        $output .= "Description: " . htmlspecialchars($oldValue['Description'] ?? 'N/A') . "<br>";
                                        $changesExist = true;
                                    }
                                    $oldFormattedPrice = number_format((float)($oldValue['Price'] ?? 0), 2);
                                    $newFormattedPrice = number_format((float)($newValue['Price'] ?? 0), 2);

                                    if ($oldFormattedPrice !== $newFormattedPrice) {
                                        $output .= "Price: " . htmlspecialchars($oldFormattedPrice) . "<br>";
                                        $changesExist = true;
                                    }
                                    if (($oldValue['QuantityAvailable'] ?? '') !== ($newValue['QuantityAvailable'] ?? '')) {
                                        $output .= "QuantityAvailable: " . htmlspecialchars($oldValue['QuantityAvailable'] ?? 'N/A');
                                        $changesExist = true;
                                    }

                                    // If changes exist, display them; otherwise, show 'No changes'
                                    if ($changesExist) {
                                        echo $output;
                                    } else {
                                        echo "No changes";
                                    }
                                } else {
                                    echo htmlspecialchars($log['OldValue'] ?? 'N/A');
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $changesExist = false;

                                // Decode JSON from NewValue column
                                $newValueDecoded = json_decode($log['NewValue'], true); // decode the NewValue JSON

                                if ($newValueDecoded) {
                                    $output = '';

                                    // Display the fields that have new values in a clean format
                                    if (!empty($newValueDecoded['ProductName'])) {
                                        $output .= "ProductName: " . htmlspecialchars($newValueDecoded['ProductName']) . "<br>";
                                        $changesExist = true;
                                    }
                                    if (!empty($newValueDecoded['Description'])) {
                                        $output .= "Description: " . htmlspecialchars($newValueDecoded['Description']) . "<br>";
                                        $changesExist = true;
                                    }

                                    // Format the price
                                    if (!empty($newValueDecoded['Price'])) {
                                        $formattedPrice = number_format((float)$newValueDecoded['Price'], 2); // format price to 2 decimals
                                        $output .= "Price: " . htmlspecialchars($formattedPrice) . "<br>";
                                        $changesExist = true;
                                    }

                                    if (!empty($newValueDecoded['QuantityAvailable'])) {
                                        $output .= "QuantityAvailable: " . htmlspecialchars($newValueDecoded['QuantityAvailable']) . "<br>";
                                        $changesExist = true;
                                    }

                                    // If changes exist, display them; otherwise, show 'No changes'
                                    if ($changesExist) {
                                        echo $output;
                                    } else {
                                        echo "No changes";
                                    }
                                } else {
                                    // Handle cases where NewValue is not a valid JSON or is empty
                                    echo htmlspecialchars($log['NewValue'] ?? 'N/A');
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Order Status History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Order ID</th>
                        <th>Order Status</th>
                        <th>Payment Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $result_order_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['Full_Name']); ?></td>
                            <td><?php echo htmlspecialchars($log['OrderID']); ?></td>
                            <td><?php echo htmlspecialchars($log['OldOrderStatus']); ?> to <?php echo htmlspecialchars($log['NewOrderStatus']); ?></td>
                            <td><?php echo htmlspecialchars($log['OldPaymentStatus']); ?> to <?php echo htmlspecialchars($log['NewPaymentStatus']); ?></td>
                            <td><?php echo htmlspecialchars($log['UpdateTimestamp']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> MAPARCO. All rights reserved.</p>
        </footer>
    </section>
</body>

</html>