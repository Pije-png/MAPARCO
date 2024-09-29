<?php
include '../../connection.php';

// Initialize messages
$global_message = "";
$global_update_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the add product form is submitted
    if (isset($_POST['submit'])) {
        // Process the form data
        $productName = $_POST['productName'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $quantityAvailable = $_POST['quantityAvailable'];

        // File upload handling
        $target_dir = "uploads/"; // Directory where the file will be stored
        $original_file_name = basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $original_file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $global_error_message = ""; // Initialize error message variable

        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            $global_error_message = "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $global_error_message = "<i class='bi bi-exclamation-circle-fill'></i> File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["photo"]["size"] > 8388608) { // Updated to 8MB limit as requested earlier
            $global_error_message = "<i class='bi bi-exclamation-circle-fill'></i> Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $global_error_message = "<i class='bi bi-exclamation-circle-fill'></i> Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // If file exists, rename it
        if (file_exists($target_file)) {
            $file_name_without_ext = pathinfo($original_file_name, PATHINFO_FILENAME); // Get file name without extension
            $target_file = $target_dir . $file_name_without_ext . '_' . time() . '.' . $imageFileType; // Add timestamp to the file name
        }

        // Attempt to upload the file if no errors
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // File upload was successful, now proceed to insert product into database
                $sql = "INSERT INTO products (ProductName, Photo, Description, Price, QuantityAvailable) 
                        VALUES ('$productName', '$target_file', '$description', '$price', '$quantityAvailable')";

                if ($conn->query($sql) === TRUE) {
                    $global_message = "New product added successfully.";
                } else {
                    $global_message = "Error adding product: " . $conn->error;
                }
            } else {
                $global_message = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Check if the update form is submitted
if (isset($_POST['update'])) {
    // Process the form data and update the product in the database
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantityAvailable = $_POST['quantityAvailable'];

    // Update product query
    $sql = "UPDATE products SET 
                ProductName = '$productName',
                Description = '$description',
                Price = '$price',
                QuantityAvailable = '$quantityAvailable'
                WHERE ProductID = $productId";

    if ($conn->query($sql) === TRUE) {
        $global_update_message = "Product updated successfully!";
    } else {
        $global_update_message = "Error updating product: " . $conn->error;
    }
}

// Check if the delete form is submitted
if (isset($_POST['delete'])) {
    // Process the form data and delete the product from the database
    $productID = $_POST['productID'];

    // Delete product query
    $sql = "DELETE FROM products WHERE ProductID = $productID";

    if ($conn->query($sql) === TRUE) {
        $global_update_message = "Product deleted successfully!";
    } else {
        $global_update_message = "Error deleting product: " . $conn->error;
    }
}


// SQL query to select all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Get the number of products
$product_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/MAPARCO.png" />
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/manages.css">
</head>

<body class="bg bg-light">

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <div class="customer-container">
            <div class="container-fluid">
                <div class="pt-2 pb-5">
                    <div class="head pb-2">
                        <div class="arrow left"></div>
                        <p class="text-center h4 fw-bold text-light" style="font-style: italic; font-family: cursive; "><i class="fa-solid fa-fire"></i> List of Products</p>
                        <div class="arrow right"></div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="status-messages">
                                <?php if (!empty($global_update_message)) {
                                    echo "<div class='status-message'>" . htmlspecialchars($global_update_message) . " <i class='bx bxs-check-circle text-success'></i></div>";
                                } ?>
                            </div>
                            <div class="status-messages">
                                <?php if (isset($global_message) && !empty($global_message)) {
                                    echo "<div class='status-message'>" . htmlspecialchars($global_message) . " <i class='bx bxs-check-circle text-success'></i></div>";
                                } ?>
                            </div>
                            <div class="orders-table-container">
                                <table class="admin-dashboard">
                                    <thead>
                                        <tr class="fw-bold fs-5 bg bg-success text-light">
                                            <th colspan="6">Products
                                                <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $product_count; ?></span>
                                            </th>
                                            <th colspan="2" class="text-center">
                                                <button type="button" class="editbtn btn btn-sm btn-success border-0" onclick="openCreateModal()">+ Add</button>
                                            </th>
                                        </tr>
                                        <tr class="text-center">
                                            <!-- <th>CustomerID</th> -->
                                            <th style="width:2%"></th>
                                            <th>Photo</th>
                                            <th>Product</th>
                                            <th>Description</th>
                                            <th style="text-align: center">Price</th>
                                            <th style="text-align: center">Quantity</th>
                                            <th colspan="2" style="width:7%">Tools</th>
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
                                                echo "<td><img src='" . $row["Photo"] . "' class='img'></td>";
                                                echo "<td>" . $row["ProductName"] . "</td>";
                                                echo "<td>" . $row["Description"] . "</td>";
                                                echo "<td style='color: red; text-align: center;'> ₱" . $row["Price"] . "</td>";
                                                echo "<td style='color: blue; text-align: center'>" . $row["QuantityAvailable"] . "</td>";
                                                echo "<td>";
                                                echo "<button class='btn btn-primary btn-sm' onclick='openModal(" . $row["ProductID"] . ", \"" . $row["ProductName"] . "\", \"" . $row["Description"] . "\", " . $row["Price"] . ", " . $row["QuantityAvailable"] . ")'><i class='bx bxs-edit'></i></button> ";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<button class='btn btn-danger btn-sm' onclick='openDeleteModal(" . $row["ProductID"] . ")'><i class='bx bxs-trash'></i></button>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No products found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'modal_management/modal_product.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.querySelector('.status-message');
            if (messageElement) {
                setTimeout(() => {
                    messageElement.classList.add('fade-out');
                }, 8000);
            }
        });
    </script>
</body>

</html>