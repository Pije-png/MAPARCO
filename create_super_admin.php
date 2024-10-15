<?php
// Include your database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maparco_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define super admin details
$super_admin_username = 'superadmin';
$super_admin_email = 'superadmin@gmail.com';
$super_admin_full_name = 'Super Admin';
$super_admin_password = password_hash('supersecretpassword', PASSWORD_DEFAULT);
$super_admin_photo = 'img/profiles/default.jpg'; 

// Insert the super admin into the `admins` table
$sql = "INSERT INTO admins (Username, Email, Full_Name, Password, Is_Admin, photo) 
        VALUES ('$super_admin_username', '$super_admin_email', '$super_admin_full_name', '$super_admin_password', 1, '$super_admin_photo')";

if ($conn->query($sql) === TRUE) {
    echo "Super admin created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
