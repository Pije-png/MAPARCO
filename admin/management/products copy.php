<?php
include '../../connection.php';

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Set default values in case data is missing
$admin_username = htmlspecialchars($admin['Username'] ?? 'Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? '../path/to/default/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator');

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

// Initialize pagination variables
$rowCount = isset($_GET['rowCount']) ? (int)$_GET['rowCount'] : 10;
$allowedRowCounts = [5, 10, 25, 50, 100];
if (!in_array($rowCount, $allowedRowCounts)) {
    $rowCount = 10;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Sorting logic
$sortField = 'ProductName';  // Default sort by ProductName
$sortOrder = "ASC";  // Default to ascending order

if (isset($_GET['sortBy'])) {
    switch ($_GET['sortBy']) {
        case 'name':
            $sortField = 'ProductName';
            $sortOrder = 'ASC';
            break;
        case 'newest':
            $sortField = 'CreatedAt';  // Assuming you have a CreatedAt column
            $sortOrder = 'DESC';
            break;
        case 'oldest':
            $sortField = 'CreatedAt';
            $sortOrder = 'ASC';
            break;
    }
}

// Get current page number from the query parameter (default to 1 if not set)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Calculate the offset for the query
$offset = ($page - 1) * $rowCount;

// Add the LIMIT and OFFSET to your SQL query
$sql = "SELECT * FROM products WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (ProductName LIKE '%$search%' 
                  OR Description LIKE '%$search%')";
}

// Add sorting and limit for pagination
$sql .= " ORDER BY $sortField $sortOrder 
          LIMIT $rowCount OFFSET $offset";

$result = $conn->query($sql);

// Fetch total number of products for pagination
$total_rows_sql = "SELECT COUNT(*) AS total_count FROM products WHERE 1=1";

if (!empty($search)) {
    $total_rows_sql .= " AND (ProductName LIKE '%$search%' 
                             OR Description LIKE '%$search%')";
}

$total_rows_result = $conn->query($total_rows_sql);
$total_rows = $total_rows_result->fetch_assoc()['total_count'];

// Calculate total pages
$total_pages = ceil($total_rows / $rowCount);
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
    <link rel="stylesheet" href="css/manage2.css">
</head>

<body class="bg bg-light">

    <?php include 'sidebar.php'; ?>

    <section class="home">
        <?php include 'header.php'; ?>
        <div class="container">
            <h1 class="mt-4">Manage Products</h1>

            <div class="row mb-3">
                <div class="col">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search...">
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>
                </div>
                <div class="col text-end">
                    <form method="GET" class="d-flex">
                        <select name="rowCount" onchange="this.form.submit();" class="form-select">
                            <?php foreach ($allowedRowCounts as $count) : ?>
                                <option value="<?php echo $count; ?>" <?php echo $count == $rowCount ? 'selected' : ''; ?>>
                                    <?php echo $count; ?> rows
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (!empty($global_message)) : ?>
                <div class="alert alert-success"><?php echo $global_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($global_update_message)) : ?>
                <div class="alert alert-info"><?php echo $global_update_message; ?></div>
            <?php endif; ?>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><a href="?sortBy=name&page=<?php echo $page; ?>&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>">Product Name</a></th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Description']); ?></td>
                            <td><?php echo htmlspecialchars($row['Price']); ?></td>
                            <td><?php echo htmlspecialchars($row['QuantityAvailable']); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="productId" value="<?php echo $row['ProductID']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['ProductID']; ?>">Update</button>

                                <!-- Update Modal -->
                                <div class="modal fade" id="updateModal<?php echo $row['ProductID']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateModalLabel">Update Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <input type="hidden" name="productId" value="<?php echo $row['ProductID']; ?>">
                                                    <div class="mb-3">
                                                        <label for="productName" class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" name="productName" value="<?php echo htmlspecialchars($row['ProductName']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($row['Description']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="price" class="form-label">Price</label>
                                                        <input type="number" class="form-control" name="price" value="<?php echo htmlspecialchars($row['Price']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="quantityAvailable" class="form-label">Quantity Available</label>
                                                        <input type="number" class="form-control" name="quantityAvailable" value="<?php echo htmlspecialchars($row['QuantityAvailable']); ?>" required>
                                                    </div>
                                                    <button type="submit" name="update" class="btn btn-primary">Update Product</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item"><a class="page-link" href="?page=1&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>&sortBy=<?php echo isset($_GET['sortBy']) ? $_GET['sortBy'] : ''; ?>">First</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>&sortBy=<?php echo isset($_GET['sortBy']) ? $_GET['sortBy'] : ''; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>&sortBy=<?php echo isset($_GET['sortBy']) ? $_GET['sortBy'] : ''; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>&sortBy=<?php echo isset($_GET['sortBy']) ? $_GET['sortBy'] : ''; ?>">Next</a></li>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $total_pages; ?>&rowCount=<?php echo $rowCount; ?>&search=<?php echo htmlspecialchars($search); ?>&sortBy=<?php echo isset($_GET['sortBy']) ? $_GET['sortBy'] : ''; ?>">Last</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybSLQ4C4F5XVV38r31Izz1NSl1Mj07vXxk+Jq5U8hH3CQBqg4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-pqE8I5zJPRHxblDgKiRjSHJmvQ7moP1gmcSeTwF8/CLSAw7NCP9Havv26YOPxAqj" crossorigin="anonymous"></script>
</body>

</html>