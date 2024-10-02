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
    <link rel="stylesheet" href="address/addresss.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            include('../connection.php');

            // Fetch addresses for the logged-in user
            $sql_addresses = "SELECT * FROM addresses WHERE CustomerID = '$customer_id'";
            $result_addresses = $conn->query($sql_addresses);

            // Check if the query executed successfully
            if ($result_addresses->num_rows > 0) {
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

    <?php include 'address/modal_address.php' ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>