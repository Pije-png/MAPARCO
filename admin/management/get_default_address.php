<?php
include '../../connection.php';

if (isset($_GET['addressId'])) {
    $addressId = $_GET['addressId'];
    // SQL query to fetch default address based on address ID
    $sql = "SELECT * FROM addresses WHERE AddressID = $addressId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Echo the default shipping address
        echo $row['Description'] . ", " . $row['HouseNo'] . ", " . $row['Street'] . ", " . $row['Barangay'] . ", " . $row['City'] . ", " . $row['Province'] . ", " . $row['ZipCode'];
    } else {
        echo "Default address not found";
    }
} else {
    echo "Address ID not provided";
}

// Close the database connection
$conn->close();
?>
