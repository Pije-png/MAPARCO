<?php
include '../../connection.php';

// Check if super admin is logged in
if (!isset($_SESSION['super_admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Fetch super admin details
$super_admin_id = $_SESSION['super_admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ? AND Is_SuperAdmin = 1");
$query->bind_param("i", $super_admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

$admin_username = htmlspecialchars($admin['Username'] ?? 'Super Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'uploads/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Super Administrator');

$message = '';

// Handle the deletion of an admin
if (isset($_POST['delete_admin'])) {
    $admin_id = $_POST['admin_id'];

    // Check if the admin being deleted is not the super admin
    if ($admin_id != $super_admin_id) {
        $delete_sql = "DELETE FROM admins WHERE ID = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $admin_id);

        if ($delete_stmt->execute()) {
            $message = "<div class='alert alert-success'>Admin deleted successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $delete_stmt->error . "</div>";
        }

        $delete_stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>You cannot delete the super admin.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $email = $_POST['email'] ?? null;
    $fullName = $_POST['full_name'] ?? null;

    // Check if the photo was uploaded before accessing it
    if (isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
        $photoName = $_FILES['photo']['name'];
        $photoTmpName = $_FILES['photo']['tmp_name'];
        $photoNewPath = 'uploads/' . $photoName;

        if (move_uploaded_file($photoTmpName, $photoNewPath)) {
            // Insert admin data into the database, setting Is_Admin to 1
            $sql = "INSERT INTO admins (Username, Password, Email, Full_Name, Is_Admin, photo) 
                    VALUES (?, ?, ?, ?, 1, ?)"; // Set Is_Admin to 1 here

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $username, $password, $email, $fullName, $photoNewPath);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>New admin added successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>Failed to upload photo.</div>";
        }
    } else {
        // Handle case when no photo is uploaded (optional)
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        /* Tab menu and form styles for a smaller look */
        .tab-menu {
            display: flex;
            border-bottom: 1px solid #ddd;
        }

        .tab-menu button {
            padding: 10px 20px;
            cursor: pointer;
            background: #f4f4f4;
            border: none;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .tab-menu button.active {
            background: #007bff;
            color: white;
            border-bottom: 2px solid #007bff;
        }

        .tab-content {
            display: none;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .form-control,
        .btn {
            /* font-size: 12px; */
            padding: 5px;
        }

        .card-header h2 {
            font-size: 18px;
        }

        .card {
            /* font-size: 12px; */
        }

        table {
            /* font-size: 12px; */
        }

        table th,
        table td {
            padding: 5px;
        }

        .float-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .float-button:hover {
            background-color: #0056b3;
        }

        .admin-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .admin-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border: 1px solid dodgerblue;
        }
    </style>
</head>

<body>
    <a href="../home.php"><button class="float-button">Back to dashboard</button></a>
    <header>
        <div class="">
            <h2>Admin Management</h2>
        </div>
    </header>
    <div class="container my-4">
        <?php echo $message; ?>
        <div class="card">
            <div class="card-body">
                <div class="admin-info">
                    <img src="<?php echo $admin_photo; ?>" alt="Admin Photo">
                    <div>
                        <h2><?php echo $admin_full_name; ?></h2>
                        <p><?php echo $admin_username; ?></p>
                    </div>
                </div>
                <div class="tab-menu">
                    <button class="tab-button active" onclick="openTab(event, 'view-admins')">View Admins</button>
                    <button class="tab-button" onclick="openTab(event, 'add-admin')">Add Admin</button>
                </div>

                <!-- Tab Content: Add Admin -->
                <div id="add-admin" class="tab-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="full_name" class="form-label">Full Name:</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="photo" class="form-label">Profile Photo:</label>
                            <input type="file" name="photo" class="form-control" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">Add Admin</button>
                    </form>
                </div>

                <!-- Tab Content: View Admins -->
                <div id="view-admins" class="tab-content active">
                    <h3>List of Admins</h3>
                    <?php
                    include '../../connection.php';

                    // Fetch all admins
                    $sql_admins = "SELECT ID, Username, Email, Full_Name, photo FROM admins WHERE Is_SuperAdmin = 0";
                    $result_admins = $conn->query($sql_admins);

                    if ($result_admins && $result_admins->num_rows > 0) {
                        echo "<table class='table table-striped mt-3'>";
                        echo "<thead>";
                        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Profile Photo</th><th>Action</th></tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        while ($row = $result_admins->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['ID'] . "</td>";
                            echo "<td>" . $row['Username'] . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            echo "<td>" . $row['Full_Name'] . "</td>";
                            echo "<td><img src='" . $row['photo'] . "' alt='Profile Photo' width='40' height='40'></td>";
                            echo "<td>";
                            // Delete button
                            if ($row['ID'] != $super_admin_id) {
                                echo "<form method='POST' style='display:inline;'>";
                                echo "<input type='hidden' name='admin_id' value='" . $row['ID'] . "'>";
                                echo "<button type='submit' name='delete_admin' class='btn btn-danger btn-sm'>Delete</button>";
                                echo "</form>";
                            } else {
                                echo "<span class='text-muted'>Cannot delete Super Admin</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<div class='alert alert-info'>No admins found.</div>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openTab(event, tabId) {
            var i, tabContent, tabButtons;

            // Hide all tab content
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }

            // Remove active class from all tab buttons
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }

            // Show the selected tab content and set active class on the clicked button
            document.getElementById(tabId).style.display = "block";
            event.currentTarget.classList.add("active");
        }

        // Show the first tab content by default
        document.getElementById("view-admins").style.display = "block";
    </script>
</body>

</html>