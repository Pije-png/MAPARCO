<?php
include '../../connection.php'; // Include your database connection file

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Get the number of products
$product_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Inventory</title>
</head>
<style>
    .img {
        max-width: 50px;
        object-fit: contain;
    }
</style>

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
                    <h4 class="text-center">Inventory</h4>
                </div>
                <div class="orders-table-container">
                    <table class="admin-dashboard">
                        <thead>
                            <tr class="fw-bold fs-5 bg bg-success text-light">
                                <th colspan="7">Product Lists
                                <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $product_count; ?></span>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <th style="width:2%"></th>
                                <th>Product Name</th>
                                <th>Photo</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Stocks</th>
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
                                    echo "<td>" . $row['ProductName'] . "</td>";
                                    echo "<td><img src='" . $row['Photo'] . "' alt='Product Image' style='width:50px;height:50px;'></td>";
                                    echo "<td>" . $row['Description'] . "</td>";
                                    echo "<td class='text-danger'>₱" . $row['Price'] . "</td>";
                                    echo "<td class='text-primary'>" . $row['QuantityAvailable'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No products found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

<?php
$conn->close();
?>