<?php
include '../../connection.php';

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
    <!-- <link rel="stylesheet" href="Manage.css"> -->
    <style>
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
                    <h4 class="text-center">Registered</h4>
                </div>
                <div class="orders-table-container">
                    <table class="admin-dashboard">
                        <thead>
                            <tr class="fw-bold fs-5 bg bg-success text-light">
                                <th colspan="7">Customers Lists
                                    <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $customers_count; ?></span>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <!-- <th>CustomerID</th> -->
                                <th style="width:2%"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined on: </th>
                                <!-- <th>Tools</th> -->
                            </tr>
                        </thead>
                        <tbody class="bg bg-light text-center">
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
                </div>
            </div>
        </div>
    </section>

    <script>
        const toggle = document.querySelector(".toggle");
        const sidebar = document.querySelector(".sidebar");
        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });
    </script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>