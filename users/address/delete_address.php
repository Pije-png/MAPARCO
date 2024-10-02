<?php
// Check if addressID is set and not empty
if (isset($_POST['addressID']) && !empty($_POST['addressID'])) {
    // Sanitize the input to prevent SQL injection
    $addressID = $_POST['addressID'];

    // Prepare and execute the SQL query to delete the address
    $sql = "DELETE FROM addresses WHERE AddressID = '$addressID'";
    if ($conn->query($sql) === TRUE) {
        // Address deleted successfully
        // Redirect to the addresses page or perform any other action as needed
        header("Location: ../address.php");
        exit();
    } else {
        // Error occurred while deleting the address
        echo "Error: " . $conn->error;
    }
} else {
    // Redirect or display an error message if addressID is not set or empty
    header("Location: ../address.php"); // Redirect to addresses page
    exit();
}
