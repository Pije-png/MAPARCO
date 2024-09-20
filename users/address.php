<?php
include('../connection.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$customerID = $_SESSION['customer_id'];
$errors = array();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    if (isset($_POST['fullName'], $_POST['phoneNumber'], $_POST['description'], $_POST['street'], $_POST['barangay'], $_POST['city'], $_POST['province'], $_POST['zipCode'], $_POST['AddedAt'], $_POST['UpdatedAt'])) {
        $fullName = $_POST['fullName'];
        $phoneNumber = $_POST['phoneNumber'];
        $description = $_POST['description'];
        $houseNo = !empty($_POST['houseNo']) ? $_POST['houseNo'] : 'houseNo. N/A';
        $street = $_POST['street'];
        $barangay = $_POST['barangay'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $zipCode = $_POST['zipCode'];
        $AddedAt = $_POST['AddedAt'];
        $UpdatedAt = $_POST['UpdatedAt'];
        
        // Check if the user already has addresses
        $checkAddressQuery = "SELECT COUNT(*) as count FROM addresses WHERE CustomerID = '$customerID'";
        $result = $conn->query($checkAddressQuery);
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            // No existing addresses, set this as the default address
            $isDefault = 1;
        } else {
            // Check if the address should be set as default
            $isDefault = isset($_POST['isDefault']) ? 1 : 0;
        }

        // Add a new address
        $sql = "INSERT INTO addresses (CustomerID, FullName, PhoneNumber, Description, HouseNo, Street, Barangay, City, Province, ZipCode, AddedAt, UpdatedAt, IsDefault) 
                VALUES ('$customerID', '$fullName', '$phoneNumber', '$description', '$houseNo', '$street', '$barangay', '$city', '$province', '$zipCode', '$AddedAt', '$UpdatedAt', '$isDefault')";

        if ($conn->query($sql) === TRUE) {
            if ($isDefault) {
                // Get the newly added address ID
                $newAddressID = $conn->insert_id;

                // Update the previous default address to remove the default flag
                $updatePrevDefaultQuery = "UPDATE addresses SET IsDefault = 0 WHERE CustomerID = '$customerID' AND AddressID != '$newAddressID'";
                $conn->query($updatePrevDefaultQuery);
            }
        } else {
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $errors[] = "All form fields are required.";
    }
}

// Check if the delete form is submitted
if (isset($_POST['delete'])) {
    // Process the form data and delete the product from the database
    $addressID = $_POST['addressID'];

    // Delete product query
    $sql = "DELETE FROM addresses WHERE AddressID = $addressID";

    if ($conn->query($sql) === TRUE) {
        echo "";
    } else {
        echo "Error deleting address: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addresses</title>
    <!-- <link rel="stylesheet" href="address/add.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        /* Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            display: flex;
            flex-direction: row;
            margin-top: 80px;
            position: relative;
        }

        .address-card {
            width: 700px;
            margin: auto;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .address p {
            margin: 0;
            line-height: 1.5;
            font-size: 12px;
        }

        .header h5 {
            color: green;
            font-weight: 500;
        }

        .headers {
            display: flex;
            justify-content: space-between;
        }

        .address-info p {
            line-height: 1.2;
        }

        .default-label {
            color: green;
            border: 1px solid green;
            padding: 0px 10px;
            font-size: 10px;
        }

        .set_default {
            border: 1px solid #ccc;
            color: #888;
        }

        .address-actions .mixed {
            position: absolute;
            right: 10px;
        }

        .address-actions .mix {
            position: absolute;
            right: 10px;
            padding-top: 20px;
        }

        .default-label {
            font-size: 12px;
        }

        .address .set_default {
            font-size: 12px;
        }

        .address .edit-address,
        .address a {
            font-size: 12px;
        }

        .address-actions a {
            text-decoration: none;
            color: red;
        }

        .edit-address {
            background: none;
            border: 0;
            color: #08f;
            outline: none;
            white-space: nowrap;
        }
    </style>
    <style>
        /* Modal CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 90px auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 450px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .modal-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #333;
            margin: 0;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        /* Add CSS styles for labels */
        .modal-content label {
            display: block;
            margin-bottom: 0px;
        }

        /* Adjust input fields */
        #createProductModal .modal-content input[type="text"] {
            width: calc(100% - 12px);
            padding: 5px;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 0px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 13px;
        }

        #editForm .modal-content input[type="text"] {
            width: calc(100% - 12px);
            padding: 5px;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 13px;
        }

        .modal-footer {
            text-align: right;
        }

        .modal-footer button {
            padding: 10px 20px !important;
            background-color: #ee4d2d !important;
            color: #fff !important;
            border: none !important;
            border-radius: 4px !important;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-footer button:hover {
            background-color: #d63a1e !important;
        }

        .modal-content #saveEdit {
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content #saveEdit:hover {
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.9);
        }

        .close-modal {
            color: red;
            float: right;
            font-size: 28px;
            font-weight: bold;
            border: none;
            background-color: transparent;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: lightcoral;
            text-decoration: none;
            cursor: pointer;
        }

        /* ============================================================================================== */
        /* ============================================================================================== */
        /* ============================================================================================== */
        .modal-header {
            background-color: #fffefb;
            /* border: 1px solid rgba(224, 168, 0, .4); */
            border-radius: 2px;
            display: flex;
            margin-bottom: 15px;
            position: relative;
        }

        .modal-header h5 {
            color: #888;
            font-weight: 700;
            margin: 0;
        }

        .right-content {
            position: absolute;
            right: 20px;
        }

        .modal-content .form-group label {
            display: block;
            margin-bottom: 0px;
            margin-top: 0px;
            font-size: 10px;
        }

        .address-container button:hover {
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
        }

        .modal-content .form-group {
            margin-bottom: .5rem;
        }

        .modal-content .form-check {
            margin-top: .5rem;
        }

        .modal-content .footer {
            margin-top: 1rem;
        }

        .modal-content .footer .form-group {
            margin-bottom: 0;
        }
    </style>
    <style>
       @media (max-width: 510px) {

            .header button {
                font-size: 10px;
            }

            .address h6 {
                font-size: 13px;
            }

            .address p {
                font-size: 11px;
            }

            .default-label {
                font-size: 11px;
            }

            .address .set_default {
                font-size: 11px;
            }

            .address .edit-address,
            .address a {
                font-size: 11px;
            }

            .address-actions .mixed {
                position: absolute;
                right: 5px;
            }

            .address-card {
                min-height: 420px;
            }
            
            .modal-content.create{
                margin-top: 0px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbars/navbar.php' ?>
    <div class="container">
        <div class="address-card p-4 bg-success bg-opacity-10 border border-success border-start rounded">
            <div class="header">
                <h5 class='text-success fw-bold'><i class="fa-solid fa-location-dot"></i> Addresses</h5>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createProductModal">New Address</button>
            </div>

            <?php
            // Fetch user data from the session
            $customer_id = $_SESSION['customer_id'];

            // Include your database connection configuration
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "MAPARCO";            

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch addresses for the logged-in user
            $sql_addresses = "SELECT * FROM addresses WHERE CustomerID = '$customer_id'";
            $result_addresses = $conn->query($sql_addresses);

            // Check if the query executed successfully
            if ($result_addresses->num_rows > 0) {
                // Output data of each row
                while ($row = $result_addresses->fetch_assoc()) {
                    echo "<div class='address row'>";
                    echo "<div class='address-info'>";
                    echo "<div class='headers mt-4'>";

                    echo "<h6>" . $row["FullName"] . " | <span class='phone-number'>" . $row["PhoneNumber"] . "</span></h6>";

                    echo "<div class='address-actions'>";
                    echo "<div class='mixed'>";
                    echo "<button class='edit-address' data-address='" . json_encode($row) . "'>Edit</button>";
                    echo "<a href='#' onclick='openDeleteModal(" . $row["AddressID"] . ")' class='delete-link mr-2 '>Delete</a>";
                    echo "</div>";

                    // Check if the address is the default one
                    if ($row["IsDefault"] != 1) {
                        // Render the "Set as Default" button
                        echo "<div class='mix'>";

                        echo "<form action='set_default_address.php' method='POST'>";
                        echo "<input type='hidden' name='address_id' value='" . $row["AddressID"] . "'>";
                        echo "<button type='submit' class='set_default' name='set_default'>Set as Default</button>";
                        echo "</form>";
                        echo "</div>";
                    }

                    echo "</div>"; // Close address-actions
                    echo "</div>"; // Close address-info
                    echo "<p class='mb-1'>" . $row["Description"] . "</p>";
                    echo "<p class='mb-0'>" . $row["Barangay"] . ", " . $row["City"] . ", " . $row["Province"] . ", " . $row["ZipCode"] . "</p>";
                    // Add label for default address if it's the default address
                    if ($row["IsDefault"] == 1) {
                        echo "<label class='default-label mb-0'>Default</label>";
                    }
                    echo "</div>"; // Close address
                }
            } else {
                echo "<p class='no-address'>No addresses found.</p>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Edit form -->
    <div id="editForm" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5> Update Address</h5>
                    <div class="right-content">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span class="close-modal">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="editAddressForm">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Full Name" required>
                    <label for="phoneNumber">Phone Number:</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required><br>
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" placeholder="Description" required><br>
                    <label for="houseNo">House No:</label>
                    <input type="text" id="houseNo" name="houseNo" placeholder="House No" required><br>
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" placeholder="Street" required><br>
                    <label for="barangay">Barangay:</label>
                    <input type="text" id="barangay" name="barangay" placeholder="Barangay" required><br>
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" placeholder="City" required><br>
                    <label for="province">Province:</label>
                    <input type="text" id="province" name="province" placeholder="Province" required><br>
                    <label for="zipCode">Zip Code:</label>
                    <input type="text" id="zipCode" name="zipCode" placeholder="Zip Code" required><br>
                </form>
                <button id="saveEdit" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;">Save Changes</button>
            </div>
        </div>
    </div>

    <!--Create product modal -->
    <div class="modal" id="createProductModal">
        <div class="modal-dialog">
            <div class="modal-content create">
                <div class="modal-header">
                    <h5>New Address</h5>
                    <div class="right-content">
                        <button type="button" class="close-modal" data-dismiss="modal">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <!-- Display any validation errors here -->
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error) : ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <!-- Name & Phone no. -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fullName">Full Name:</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phoneNumber">Phone No:</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required>
                            </div>
                        </div>
                        <!-- House no. & Zip code -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="houseNo">House No:</label>
                                <input type="text" class="form-control" id="houseNo" name="houseNo" placeholder="House No">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="zipCode">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="Zip Code" required>
                            </div>
                        </div>
                        <!-- Street & Barangay -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="street">Street:</label>
                                <input type="text" class="form-control" id="street" name="street" placeholder="Street" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="barangay">Barangay:</label>
                                <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay" required>
                            </div>
                        </div>
                        <!-- City/ Municipality & Province -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="city">City/ Municipality:</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City/ Municipality" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="province">Province:</label>
                                <input type="text" class="form-control" id="province" name="province" placeholder="Province" required>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="AddedAt" name="AddedAt" value="<?php echo (date("Y-m-d H:i:s")); ?>">
                            <input type="hidden" class="form-control" id="UpdatedAt" name="UpdatedAt" value="<?php echo (date("Y-m-d H:i:s")); ?>">
                        </div>
                        <!-- Set as Default Address -->
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="isDefault" name="isDefault">
                            <label class="form-check-label" for="isDefault">Set as Default Address</label>
                        </div>
                        <button class="btn-submit w-100" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Popup -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="color: tomato;">Delete</h5>
                    <div class="right-content">
                        <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
                    </div>
                </div>
                <div class="content">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" id="deleteAddressId" name="addressID">
                        <p>Are you sure you want to delete this address?</p>
                        <input type="submit" name="delete" value="Delete Address" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // Function to open the create address modal
        function openCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "block";
        }

        // Function to close the create address modal
        function closeCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "none";
        }

        // Function to open the create address modal
        function openUpdateModal() {
            var editForm = document.getElementById("editForm");
            editForm.style.display = "block";
        }

        // Function to open delete modal with product ID
        function openDeleteModal(addressId) {
            document.getElementById("deleteAddressId").value = addressId;
            deleteModal.style.display = "block";
        }

        // Function to close delete modal
        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        // Function to close the create address modal
        function closeUpdateModal() {
            var editForm = document.getElementById("editForm");
            editForm.style.display = "none";
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

    <script>
        // Function to populate edit form with address details
        // Function to populate edit form with address details
        // Function to populate edit form with address details
        function populateEditForm(addressData) {
            var editForm = document.getElementById("editAddressForm");
            editForm.innerHTML = `
        <input type="hidden" name="addressID" value="${addressData.AddressID}">
        <input type="hidden" name="addressID" value="${addressData.AddressID}">

        <div style="display: flex;">
        <div style="width: 10px;"></div>
            <div style="display: flex; flex-direction: column; margin-right: 10px;">
                <label for="fullName" style="font-size: 10px;">Full Name:</label>
                <input type="text" name="fullName" value="${addressData.FullName}" placeholder="Full Name" required title="Full Name" style="width: calc(100% - 5px);">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="phoneNumber" style="font-size: 10px;">Phone No:</label>
                <input type="text" name="phoneNumber" value="${addressData.PhoneNumber}" placeholder="Phone Number" required title="Phone Number" style="width: calc(100% - 5px);">
            </div>
        </div>

        <div style="display: flex;">
            <div style="width: 10px;"></div>
            <div style="display: flex; flex-direction: column; margin-right: 10px;">
                <label for="houseNo" style="font-size: 10px;">House No:</label>
                <input type="text" name="houseNo" value="${addressData.HouseNo}" placeholder="House No" required title="House No." style="width: calc(100% - 5px);">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="zipCode" style="font-size: 10px;">Zip Code:</label>
                <input type="text" name="zipCode" value="${addressData.ZipCode}" placeholder="Zip Code" required title="Zip Code" style="width: calc(100% - 5px);">
            </div>
        </div>
        <div style="display: flex;">
            <div style="width: 10px;"></div>
            <div style="display: flex; flex-direction: column; margin-right: 10px;">
                <label for="street" style="font-size: 10px;">Street:</label>
                <input type="text" name="street" value="${addressData.Street}" placeholder="Street" required title="Street" style="width: calc(100% - 5px);">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="barangay" style="font-size: 10px;">Barangay:</label>
                <input type="text" name="barangay" value="${addressData.Barangay}" placeholder="Barangay" required title="Barangay" style="width: calc(100% - 5px);">
            </div>
        </div>
        <div style="display: flex;">
            <div style="width: 10px;"></div>
            <div style="display: flex; flex-direction: column; margin-right: 10px;">
                <label for="city" style="font-size: 10px;">City/ Municipality:</label>
                <input type="text" name="city" value="${addressData.City}" placeholder="City/ Municipality" required title="City" style="width: calc(100% - 5px);">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="province" style="font-size: 10px;">Province:</label>
                <input type="text" name="province" value="${addressData.Province}" placeholder="Province" required title="Province" style="width: calc(100% - 5px);">
            </div>
        </div>
        <label for="description" style="font-size: 10px; margin-top:10px">Description:</label>
        <input type="text" name="description" value="${addressData.Description}" placeholder="Description" required title="Description"><br>
        `;
        }

        // Get all edit buttons
        var editButtons = document.querySelectorAll('.edit-address');

        // Loop through each edit button and add click event listener
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var addressData = JSON.parse(this.getAttribute('data-address'));
                populateEditForm(addressData);
                document.getElementById("editForm").style.display = "block";
            });
        });

        // Close the edit form when the close button is clicked
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById("editForm").style.display = "none";
        });

        // Handle save changes button click event
        document.getElementById('saveEdit').addEventListener('click', function() {
            // Prepare form data
            var formData = new FormData(document.getElementById('editAddressForm'));

            // Create XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure AJAX request
            xhr.open('POST', 'edit_address.php', true);

            // Define what happens on successful data submission
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle success
                    document.getElementById("editForm").style.display = "none";
                    // Reload the page or update the address list as needed
                    location.reload(); // Example: reload the page
                } else {
                    // Handle error
                    console.error('Error while updating address:', xhr.statusText);
                }
            };

            // Define what happens in case of error
            xhr.onerror = function() {
                console.error('AJAX request failed.');
            };

            // Send form data
            xhr.send(formData);
        });
    </script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>