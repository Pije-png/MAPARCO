<?php
include('../connection.php');

// Fetch user data from the database
$customer_id = $_SESSION['customer_id'];
$sql = "SELECT Name, Email FROM customers WHERE CustomerID = '$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, retrieve data
    $row = $result->fetch_assoc();
    $name = $row["Name"];
    $email = $row["Email"];
} else {
    // User not found, handle error
    echo "Error: User not found";
}

// Close the database connection
$conn->close();
?>
<!-- Edit Profile Form -->
<form id="editProfileForm" method="POST">
    <div class="form-group">
        <!--<small class="text-secondary">Touch the background to close.</small><br>-->
        <label for="name" style="font-size: 10px;">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>">
    </div>
    <div class="form-group">
        <label for="email" style="font-size: 10px;">Email:</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
    </div>
    <button type="submit" id="saveEdit" class="btn btn-primary mt-3">Save Changes</button>
</form>
<script>
    $(document).ready(function() {
        $('#editProfileForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update_profile.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#editProfileModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                }
            });
        });
    });
</script>
<style>
    .modal-content {
        padding-bottom: 20px;
        position: relative;
        padding-right: 100px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        max-width: 450px;
        margin: 100px auto
    }

    .close {
        color: red;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: lightcoral;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-header h5 {
        color: #888;
        font-weight: bold;
        margin: 0;
    }

    #editProfileForm label{
        margin-bottom: 0px;
    }

    /* Adjust input fields */
    #editProfileForm input[type="text"],
    #editProfileForm input[type="email"] {
        width: calc(100% - 12px);
        padding: 5px;
        margin-left: 5px;
        margin-right: 5px;
        border: 1px solid #ccc;
        /* border-radius: 4px; */
        box-sizing: border-box;
        font-size: 13px;
    }

    #saveEdit {
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 2px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
        /* background-color: DodgerBlue !important; */
    }
</style>