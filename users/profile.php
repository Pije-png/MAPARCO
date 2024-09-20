<?php
include('../connection.php');

$customer_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = __DIR__ . '/users/uploads/';
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    if (isset($_FILES["profile_pic"]) && !empty($_FILES["profile_pic"]["tmp_name"])) {
        // Process file upload
        if ($_FILES["profile_pic"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // Update the database with the filename
                $profile_pic_filename = basename($_FILES["profile_pic"]["name"]);
                $sql = "UPDATE customers SET ProfilePicFilename = '$profile_pic_filename' WHERE CustomerID = '$customer_id'";

                if ($conn->query($sql) === TRUE) {
                    // Redirect back to profile page
                    header("Location: profile.php");
                    exit;
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Please select a file to upload.";
    }
}

// Handle remove photo request
if (isset($_POST['remove_pic'])) {
    $sql = "UPDATE customers SET ProfilePicFilename = NULL WHERE CustomerID = '$customer_id'";
    if ($conn->query($sql) === TRUE) {
        // Remove the file from the filesystem
        if (!empty($row["ProfilePicFilename"])) {
            unlink($target_dir . $row["ProfilePicFilename"]);
        }
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch user data from the database
$sql = "SELECT Name, Email, create_on, ProfilePicFilename FROM customers WHERE CustomerID = '$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, retrieve data
    $row = $result->fetch_assoc();
    $name = $row["Name"];
    $email = $row["Email"];
    $created_on = $row["create_on"];
    $profile_pic = !empty($row["ProfilePicFilename"]) ? 'users/uploads/' . $row["ProfilePicFilename"] : 'default_profile.jpg';
} else {
    // User not found, handle error
    echo "Error: User not found";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .profile {
            margin: 80px auto;
            max-width: 650px;
        }

        .hstack img {
            width: 15rem;
        }

        @media (max-width: 510px) {
            .hstack {
                display: flex;
                flex-direction: column;
            }

            .fullname h5 {
                font-size: 15px;
                font-weight: 500;
            }

            .fullname p {
                font-size: 12px;
            }

            .hstack img {
                width: 10rem;
            }

            .upload-form .pic input,
            .upload-form .btns button,
            .profile .hstack button {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbars/navbar.php'; ?>
    <div class="container">
        <div class="profile row-1 p-3 bg-success bg-opacity-10 border border-success rounded">
            <div class="hstack">
                <h4 class="fw-bold text-success"><i class="fa-solid fa-user"></i> Profile</h4>
                <button class="btn btn-primary ms-auto" id="editProfileBtn">Edit Profile</button>
            </div>
            <div class="hstack">
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-image rounded">
                <div class="body row">
                    <div class="fullname p-4">
                        <h5 class="card-title"><?php echo $name; ?></h5>
                        <p class="card-text"><?php echo $email; ?></p>
                        <p class="card-text">Joined on: <?php echo date("F j, Y", strtotime($created_on)); ?></p>
                    </div>
                    <div class="upload-form p-4">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="row">
                            <div class="pic row">
                                <input type="file" class="custom-file-input col" id="profile_pic" name="profile_pic">
                            </div>
                            <div class="btns mt-2">
                                <?php if (!empty($row["ProfilePicFilename"])) : ?>
                                    <button type="submit" class="btn btn-primary btn-sm col-12">Update Photo</button>
                                    <button type="submit" class="btn btn-danger btn-sm col-12 mt-1" name="remove_pic">Remove Photo</button>
                                <?php else : ?>
                                    <button type="submit" class="btn btn-primary btn-sm col-12">Upload</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="content">
                        <div class="modal-header row">
                            <h5 class="modal-title text-success col-11" id="editProfileModalLabel"><i class="fa-solid fa-user"></i> Edit Profile</h5>
                        </div>
                    </div>
                    <div class="modal-body" id="editProfileContainer">
                        <!-- Edit Profile Form will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#editProfileBtn').click(function() {
                $.ajax({
                    url: 'edit_profile.php',
                    type: 'GET',
                    success: function(response) {
                        $('#editProfileContainer').html(response);
                        $('#editProfileModal').modal('show');
                    }
                });
            });
        });
    </script>
</body>

</html>
