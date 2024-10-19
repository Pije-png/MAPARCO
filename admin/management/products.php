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

// Initialize messages
$global_message = "";
$global_update_message = "";

// Function to log activity
if (!function_exists('logActivity')) {
    function logActivity($conn, $admin_id, $action, $product_id, $oldValue = null, $newValue = null)
    {
        // If the action is 'Added', set OldValue to NewValue
        if ($action === 'Added') {
            $oldValue = $newValue;  // Set OldValue to be the same as NewValue
        }

        $stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action, product_id, OldValue, NewValue) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $admin_id, $action, $product_id, $oldValue, $newValue);
        $stmt->execute();
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add product section
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

        // Attempt to upload file if no errors
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // Insert product into the database
                $sql = "INSERT INTO products (ProductName, Photo, Description, Price, QuantityAvailable) 
                        VALUES ('$productName', '$target_file', '$description', '$price', '$quantityAvailable')";

                if ($conn->query($sql) === TRUE) {
                    // Prepare NewValue data in JSON format
                    $newValue = json_encode([
                        'ProductName' => $productName,
                        'Description' => $description,
                        'Price' => $price,
                        'QuantityAvailable' => $quantityAvailable
                    ]);

                    // Log the activity with the NewValue as both OldValue and NewValue for "Added" action
                    logActivity($conn, $admin_id, "Added", $conn->insert_id, null, $newValue);

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

function logActivity($conn, $admin_id, $action, $product_id, $oldValue = null, $newValue = null)
{
    $stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action, product_id, OldValue, NewValue) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $admin_id, $action, $product_id, $oldValue, $newValue);
    $stmt->execute();
}

// Update product section
if (isset($_POST['update'])) {
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantityAvailable = $_POST['quantityAvailable'];

    // Fetch old values for logging
    $oldProduct = $conn->query("SELECT * FROM products WHERE ProductID = $productId")->fetch_assoc();

    $oldValue = json_encode([
        'ProductName' => $oldProduct['ProductName'],
        'Description' => $oldProduct['Description'],
        'Price' => $oldProduct['Price'],
        'QuantityAvailable' => $oldProduct['QuantityAvailable']
    ]);

    $newValue = json_encode([
        'ProductName' => $productName,
        'Description' => $description,
        'Price' => $price,
        'QuantityAvailable' => $quantityAvailable
    ]);

    // Update product query
    $sql = "UPDATE products SET 
                ProductName = '$productName',
                Description = '$description',
                Price = '$price',
                QuantityAvailable = '$quantityAvailable'
                WHERE ProductID = $productId";

    if ($conn->query($sql) === TRUE) {
        // Log just "Updated"
        logActivity($conn, $admin_id, "Updated", $productId, $oldValue, $newValue);
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
        // Log the activity with just "Deleted"
        logActivity($conn, $admin_id, "Deleted", $productID);
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
    <link rel="stylesheet" href="css/manage6.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <?php include 'header.php'; ?>
        <div class="customer-container">
            <div class="container-fluid">
                <div class="mb-5 mt-5 py-5 px-3">
                    <div class="head pb-2">
                        <div class="arrow left"></div>
                        <p class="h3 fw-bold text-light"
                            style="font-family: cursive;"><i class="fa-solid fa-fire"></i>
                            List of Products
                        </p>
                        <div class="arrow right"></div>
                    </div>
                    <!-- <div class="card rounded-0"> -->
                    <div class="">
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
                        <table class="admin-dashboard">
                            <thead>
                                <tr class="fw-bold fs-5 bg bg-success text-light">
                                    <th colspan="6" class="py-2">Products
                                        <span style="font-size: 12px;" class="badge text-bg-danger"><?php echo $product_count; ?></span>
                                    </th>
                                    <th colspan="2">
                                        <button type="button" class="editbtn btn btn-sm btn-success border-0" onclick="openCreateModal()">+ Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <!-- <th>CustomerID</th> -->
                                    <th style="width:2%"></th>
                                    <th>Photo</th>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th colspan="2" style="width:7%">Tools</th>
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
                                        echo "<td><img src='" . $row["Photo"] . "' class='img'></td>";
                                        echo "<td>" . $row["ProductName"] . "</td>";
                                        echo "<td>" . $row["Description"] . "</td>";
                                        echo "<td style='color: red;'> ₱" . $row["Price"] . "</td>";
                                        echo "<td style='color: blue'>" . $row["QuantityAvailable"] . "</td>";
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
                    <!-- </div> -->
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