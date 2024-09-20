<?php
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
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["photo"]["size"] > 900000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // if everything is ok, try to upload file
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["photo"]["name"]) . " has been uploaded.";
                // Insert product into database
                $sql = "INSERT INTO products (ProductName, Photo, Description, Price, QuantityAvailable) 
                VALUES ('$productName', '$target_file', '$description', '$price', '$quantityAvailable')";

                if ($conn->query($sql) === TRUE) {
                    echo "New product added successfully";
                } else {
                    echo "Error adding product: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
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
            echo "";
        } else {
            echo "Error updating product: " . $conn->error;
        }
    }

    // Check if the delete form is submitted
    if (isset($_POST['delete'])) {
        // Process the form data and delete the product from the database
        $productID = $_POST['productID'];

        // Delete product query
        $sql = "DELETE FROM products WHERE ProductID = $productID";

        if ($conn->query($sql) === TRUE) {
            echo "";
        } else {
            echo "Error deleting product: " . $conn->error;
        }
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
    <!-- <link rel="stylesheet" href="Management.css"> -->
    <style>
        .img {
            border-radius: 6px;
            max-width: 50px;
            object-fit: contain;
        }

        .update-link i,
        .delete-link i {
            font-size: 15px;
            /* Increase icon size */
            color: #007bff;
            /* Bootstrap primary color */
            transition: color 0.3s ease;
        }

        .update-link i:hover,
        .delete-link i:hover {
            color: #0056b3;
            /* Darker shade on hover */
        }

        .delete-link i {
            color: #dc3545;
            /* Bootstrap danger color for delete */
        }

        .delete-link i:hover {
            color: #c82333;
            /* Darker red on hover */
        }


        /* Modal Popup CSS */
        h3 {
            margin-bottom: 15px;
        }

        .inventory .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .inventory .modal-content {
            background-color: #fefefe;
            margin: auto;
            max-width: 600px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        /* Input fields */
        label {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
            font-size: 12px;
        }

        /* Submit button */
        input[type="submit"] {
            background-color: DodgerBlue;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            width: 100%;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body>

    <div class="inventory-modal">
        <!--Create product modal -->
        <div id="createProductModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="content">
                        <div class="modal-header row">
                            <h3 class="text-danger col-11">Product Information</h3>
                            <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeCreateModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                                <!-- Your form fields go here -->
                                <label for="productName">Product Name:</label><br>
                                <input type="text" name="productName" placeholder="Product Name" required>
                                <label for="description">Description:</label><br>
                                <input type="text" name="description" placeholder="Description" required>
                                <label for="price">Price:</label><br>
                                <input type="text" name="price" placeholder="Price" required>
                                <label for="quantityAvailable">quantityAvailable:</label><br>
                                <input type="text" name="quantityAvailable" placeholder="Quantity Available" required>
                                <label for="photo">Upload Photo:</label><br>
                                <input type="file" name="photo" required>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-primary" name="submit" value="Add Product">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Update Modal Popup -->
        <div id="updateModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="content">
                        <div class="modal-header row">
                            <h3 class="text-danger col-11">Update Information</h3>
                            <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" id="productId" name="productId">
                                <label for="productName">Product Name:</label><br>
                                <input type="text" id="productName" name="productName" required><br>
                                <label for="description">Description:</label><br>
                                <textarea id="description" name="description" required></textarea><br>
                                <label for="price">Price:</label><br>
                                <input type="text" id="price" name="price" required><br>
                                <label for="quantityAvailable">Quantity Available:</label><br>
                                <input type="text" id="quantityAvailable" name="quantityAvailable" required><br>
                                <input type="submit" name="update" value="Submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal Popup -->
        <div id="deleteModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="content">
                        <div class="modal-header row">
                            <h3 class="text-danger col-11">Delete</h3>
                            <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeDeleteModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" id="deleteProductId" name="productID">
                                <p>Are you sure you want to delete this product?</p>
                                <input type="submit" name="delete" value="Delete Product">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("updateModal");
        var deleteModal = document.getElementById("deleteModal");

        // Function to open modal with product data
        function openModal(productId, productName, description, price, quantityAvailable) {
            document.getElementById("productId").value = productId;
            document.getElementById("productName").value = productName;
            document.getElementById("description").value = description;
            document.getElementById("price").value = price;
            document.getElementById("quantityAvailable").value = quantityAvailable;
            modal.style.display = "block";
        }

        // Function to close modal
        function closeModal() {
            modal.style.display = "none";
        }

        // Function to open delete modal with product ID
        function openDeleteModal(productId) {
            document.getElementById("deleteProductId").value = productId;
            deleteModal.style.display = "block";
        }

        // Function to close delete modal
        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        // Function to open the create product modal
        function openCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "block";
        }

        // Function to close the create product modal
        function closeCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "none";
        }


        // Close the modal when user clicks outside of it
        window.onclick = function(event) {
            if (event.target == updateModal) {
                updateModal.style.display = "none";
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }
    </script>
</body>

</html>