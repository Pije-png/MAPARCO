<?php
include '../../connection.php';

// HEADER
// Initialize variables
$admin_id = null;
$super_admin_id = null;

// Check if admin or super admin is logged in
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id']; // Regular admin session
} elseif (isset($_SESSION['super_admin_id'])) {
    $super_admin_id = $_SESSION['super_admin_id']; // Super admin session
}

// Fetch admin or super admin details from the database
if ($admin_id) {
    $query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ? AND Is_Admin = 1");
    $query->bind_param("i", $admin_id);
} elseif ($super_admin_id) {
    $query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ? AND Is_SuperAdmin = 1");
    $query->bind_param("i", $super_admin_id);
}

if ($query) {
    $query->execute();
    $result = $query->get_result();
    $admin = $result->fetch_assoc();

    // Set default values in case data is missing
    $admin_username = htmlspecialchars($admin['Username'] ?? 'Admin');
    $admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png');
    $admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator');
} else {
    // If neither admin nor super admin is logged in, set defaults
    $admin_username = 'Admin';
    $admin_photo = 'path/to/default/photo.png';
    $admin_full_name = 'Administrator';
}

// HEADER

// SQL query to fetch customer data
$sql = "SELECT CustomerID, Name, Email, create_on FROM customers";
$result = $conn->query($sql);

// SQL query to select all products
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);

// Get the number of products
$customers_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Customers</title>
    <style>
        .container-fluid {
            background: linear-gradient(to bottom, MediumSeaGreen, white);
            /* background-color: #f1f1f1; */
        }

        .admin-dashboard {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
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

<body>

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <?php include 'header.php'; ?>
        <div class="container-fluid vh-100">
            <div class="mb-5 mt-5 py-5 px-3">
                <div class="head pb-2">
                    <div class="arrow left"></div>
                    <p class="h3 fw-bold text-light text-center"
                        style="font-family: cursive; ">
                        <i class="fa-solid fa-fire"></i> 
                        List of Registered
                    </p>
                    <div class="arrow right"></div>
                </div>
                <!-- <div class="orders-table-container"> -->
                <table class="admin-dashboard">
                    <thead>
                        <tr class="fw-bold fs-5 bg bg-success text-light">
                            <th colspan="7" class="py-2">Customers Lists
                                <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $customers_count; ?></span>
                            </th>
                        </tr>
                        <tr>
                            <!-- <th>CustomerID</th> -->
                            <th style="width:2%"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined on: </th>
                            <!-- <th>Tools</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_counter = 1; // Initialize row_counter

                        if ($result && $result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row_counter++ . "</td>"; // Increment row_counter
                                echo "<td>" . $row["Name"] . "</td>";
                                echo "<td>" . $row["Email"] . "</td>";
                                echo "<td>" . date("F j, Y", strtotime($row["create_on"])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No customers found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- </div> -->
            </div>
        </div>
    </section>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>