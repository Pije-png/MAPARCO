<?php
include('../../connection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $addressID = $_POST['addressID'];
    $fullName = $_POST['fullName'];
    $phoneNumber = $_POST['phoneNumber'];
    $description = $_POST['description'];
    $houseNo = $_POST['houseNo'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zipCode = $_POST['zipCode'];

    // Update the address in the database
    $sql = "UPDATE addresses SET FullName='$fullName', PhoneNumber='$phoneNumber', Description='$description', HouseNo='$houseNo', Street='$street', Barangay='$barangay', City='$city', Province='$province', ZipCode='$zipCode' WHERE AddressID='$addressID'";

    if ($conn->query($sql) === TRUE) {
        // Address updated successfully
        // Redirect or show success message
        header("Location: ../address.php");
        exit;
    } else {
        // Handle error
        echo "Error updating address: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
