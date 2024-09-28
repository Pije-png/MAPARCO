<?php
session_start();
require_once '../connection.php'; // Include your database connection file

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details from the database
$query = $conn->prepare("SELECT * FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [];
    // Check if changing profile information
    $username = isset($_POST['username']) ? $_POST['username'] : $admin['Username'];
    $email = isset($_POST['email']) ? $_POST['email'] : $admin['Email'];
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : $admin['Full_Name'];

    // Handle photo upload
    $photo = $admin['photo']; // Use existing photo if no new upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "management/uploads/";

        // Check if the uploads directory exists, create if not
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }

        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file; // Set the new photo path
        } else {
            $response['error'] = "Error uploading file.";
        }
    }

    // Update profile details in the database
    $update_query = $conn->prepare("UPDATE admins SET Username = ?, Email = ?, Full_Name = ?, photo = ? WHERE ID = ?");
    $update_query->bind_param("ssssi", $username, $email, $full_name, $photo, $admin_id);

    if ($update_query->execute()) {
        $response = [
            'success' => true,
            'full_name' => $full_name,
            'username' => $username,
            'email' => $email,
            'photo' => $photo
        ];
    } else {
        $response['success'] = false;
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        label {
            font-size: 14px;
            color: #0366d6;
        }

        .profile-container {
            margin-top: 50px;
            padding-left: 30px !important;
            margin-left: 30px !important;
        }

        .profile-sidebar {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .profile-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-header {
            margin-bottom: 30px;
        }

        .profile-header h2 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .profile-header small {
            color: #888;
        }

        .profile-actions button {
            margin-top: 10px;
        }

        .profile-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .profile-details h5 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .btn-edit {
            background-color: #0366d6;
            color: #fff;
        }

        .form-control:disabled {
            background-color: #f5f5f5;
        }

        /* Initially hide the editable form */
        .edit-form {
            display: none;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <section class="home">
        <div class="container-fluid profile-container">
            <div class="row">
                <!-- Sidebar (Profile Photo and Actions) -->
                <div class="col-md-4">
                    <div class="profile-sidebar text-center border">
                        <img src="<?= htmlspecialchars($admin['photo']) ?>" alt="Profile Photo" class="profile-photo" id="profile-photo-preview">
                        <div class="profile-header">
                            <h2><?= htmlspecialchars($admin['Full_Name']) ?></h2>
                            <small>@<?= htmlspecialchars($admin['Username']) ?></small>
                        </div>
                        <div class="profile-actions">
                            <button id="edit-btn" class="btn btn-primary btn-edit w-100 mt-2">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- Profile Details and Forms -->
                <div class="col-md-7">
                    <div id="profile-details" class="profile-details border px-4 pt-4 bg-white">
                        <h5 class="text-primary">Profile Information</h5>
                        <form id="profile-form" action="profile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($admin['Full_Name']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['Username']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['Email']) ?>" readonly>
                            </div>
                        </form>
                    </div>

                    <!-- Editable Form -->
                    <div id="edit-form" class="edit-form profile-details border border-primary px-4 py-4" style="display: none;">
                        <h5 class="text-primary">Edit Profile Information</h5>
                        <form id="edit-profile-form" action="profile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($admin['Full_Name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['Username']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['Email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" onchange="previewImage(event)">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // JavaScript to toggle between viewing and editing the profile details
        document.getElementById('edit-btn').addEventListener('click', function() {
            var profileDetails = document.getElementById('profile-details');
            var editForm = document.getElementById('edit-form');

            if (profileDetails.style.display === 'none') {
                profileDetails.style.display = 'block'; // Show non-editable details
                editForm.style.display = 'none'; // Hide the edit form
                this.textContent = 'Edit'; // Change button back to Edit
            } else {
                profileDetails.style.display = 'none'; // Hide non-editable details
                editForm.style.display = 'block'; // Show the edit form
                this.textContent = 'Cancel'; // Change button to Cancel
            }
        });

        // Preview uploaded image before submitting
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profile-photo-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Handle form submission via AJAX
        document.getElementById('edit-profile-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting the regular way

            var formData = new FormData(this); // Create a FormData object from the form

            fetch('profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the profile information without reloading the page
                        document.getElementById('full_name').value = data.full_name;
                        document.getElementById('username').value = data.username;
                        document.getElementById('email').value = data.email;

                        // Update the profile photo if it was changed
                        if (data.photo) {
                            document.getElementById('profile-photo-preview').src = data.photo;
                        }

                        alert('Profile updated successfully');
                    } else {
                        alert('Failed to update profile');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

</body>

</html>