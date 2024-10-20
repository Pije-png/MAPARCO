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

$admin_username = htmlspecialchars($admin['Username'] ?? 'Super Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'uploads/photo.png');
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        /* Tab menu styles */
        .tab-menu {
            display: flex;
            border-bottom: 1px solid #ddd;
        }

        .tab-menu button {
            padding: 10px 20px;
            cursor: pointer;
            background: #f4f4f4;
            border: none;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .tab-menu button.active {
            background: #007bff;
            color: white;
            border-bottom: 2px solid #007bff;
        }

        .tab-content {
            display: none;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
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

        td {
            padding: 10px;
            max-width: 200px;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td:hover {
            white-space: normal;
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
            border: 1px solid dodgerblue;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }

        .float-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .float-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <section>
        <a href="../home.php">
            <button class="float-button">Back to dashboard</button>
        </a>
        <header>
            <h2>Activity Log</h2>
        </header>
        <div class="container">
            <div class="admin-info">
                <img src="<?php echo $admin_photo; ?>" alt="Admin Photo">
                <div>
                    <h2><?php echo $admin_full_name; ?></h2>
                    <p><?php echo $admin_username; ?></p>
                </div>
            </div>

            <div class="tab-menu">
                <button class="tab-button active" onclick="openTab(event, 'product-actions')">Product Actions</button>
                <button class="tab-button" onclick="openTab(event, 'order-status-history')">Order Status History</button>
            </div>

            <!-- Tab Content: Product Actions -->
            <div id="product-actions" class="tab-content active">
                <h2>Product Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Product ID</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="activityLog">
                        <?php while ($log = $result_logs->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" class="rowCheckbox" onchange="updateCount()"></td>
                                <td><?php echo htmlspecialchars($log['Full_Name']); ?></td>
                                <td>
                                    <span class="badge 
                                <?php
                                if ($log['action'] == 'Added') {
                                    echo 'bg-success'; // Green badge for "Added"
                                } elseif ($log['action'] == 'Deleted') {
                                    echo 'bg-danger'; // Red badge for "Deleted"
                                } elseif ($log['action'] == 'Updated') {
                                    echo 'bg-primary'; // Blue badge for "Updated"
                                } else {
                                    echo 'bg-secondary'; // Grey badge for other actions
                                }
                                ?>
                            ">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($log['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($log['ProductName'] ?? 'Deleted'); ?></td>
                                <td><?php echo date("F j, Y, g:i a", strtotime($log['timestamp'])); ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-sm" onclick="removeRow(this)">Clear</button>
                                </td>
                            </tr>

                            </tfoot>

                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <p>Selected: <span id="selectedCount">0</span></p>
                            </td>
                            <td>
                                <button class="btn btn-secondary btn-sm" onclick="deleteSelected()">Clear All</button>
                            </td>
                        </tr>
                </table>
            </div>

            <!-- Tab Content: Order Status History -->
            <div id="order-status-history" class="tab-content">
                <h2>Orders Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Order ID</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $result_order_logs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['Full_Name']); ?></td>
                                <td><?php echo htmlspecialchars($log['OrderID']); ?></td>
                                <td><?php echo htmlspecialchars($log['OldOrderStatus']); ?> to <?php echo htmlspecialchars($log['NewOrderStatus']); ?></td>
                                <td><?php echo htmlspecialchars($log['OldPaymentStatus']); ?> to <?php echo htmlspecialchars($log['NewPaymentStatus']); ?></td>
                                <td><?php echo date("F j, Y, g:i a", strtotime($log['UpdateTimestamp'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> MAPARCO. All rights reserved.</p>
        </footer>
    </section>

    <script>
        function openTab(event, tabId) {
            var i, tabContent, tabButtons;

            // Hide all tab content
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }

            // Remove active class from all tab buttons
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].className = tabButtons[i].className.replace(" active", "");
            }

            // Show the selected tab content and set active class on the clicked button
            document.getElementById(tabId).style.display = "block";
            event.currentTarget.className += " active";
        }

        // Show the first tab content by default
        document.getElementById("product-actions").style.display = "block";
    </script>

    <script>
        function toggleSelectAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.rowCheckbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateCount();
        }

        function updateCount() {
            const checkboxes = document.querySelectorAll('.rowCheckbox:checked');
            document.getElementById('selectedCount').innerText = checkboxes.length;
        }

        function removeRow(button) {
            // Find the row that contains the button
            var row = button.parentNode.parentNode;

            // Confirm deletion
            if (confirm("Are you sure you want to delete this entry?")) {
                // Remove the row from the table
                row.parentNode.removeChild(row);
                updateCount(); // Update count after removing a row
            }
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.rowCheckbox:checked');
            if (checkboxes.length === 0) {
                alert("No rows selected for deletion.");
                return;
            }

            if (confirm("Are you sure you want to delete the selected entries?")) {
                checkboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    row.parentNode.removeChild(row);
                });
                updateCount(); // Update count after removing rows
            }
        }
    </script>
</body>

</html>