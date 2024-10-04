<?php
require_once '../connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Set default values in case data is missing
$admin_username = htmlspecialchars($admin['Username'] ?? 'Admin'); // Default username if not set
$admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png'); // Default photo if not set
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator'); // Default full name if not set
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .heade {
            background-color: #343a40;
            color: white;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .profile-info {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-right: 20px;
        }

        .profile-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 5px;
            border: 2px solid olivedrab;
        }

        .username {
            color: white;
            font-size: 12px;
            /* font-weight: bold; */
        }
    </style>
</head>

<body>
    <header class="heade">
        <div class="profile-info pt-2 p-1">
            <img src="<?= $admin_photo ?>" alt="Profile Photo" class="profile-photo">
        </div>
    </header>
</body>

</html>