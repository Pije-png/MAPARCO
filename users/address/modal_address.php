<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <title>Document</title>
</head>

<body>


    <!-- Edit form -->
    <div id="editForm" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content create">
                <div class="modal-header">
                    <h5> Update Address</h5>
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span class="close-modal">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="editAddressForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Full Name:</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number:</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="zipCode" class="form-label">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="Zip Code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="houseNo" class="form-label">House No:</label>
                                <input type="text" class="form-control" id="houseNo" name="houseNo" placeholder="House No" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="street" class="form-label">Street:</label>
                            <input type="text" class="form-control" id="street" name="street" placeholder="Street" required>
                        </div>

                        <div class="mb-3">
                            <label for="barangay" class="form-label">Barangay:</label>
                            <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City:</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="province" class="form-label">Province:</label>
                                <input type="text" class="form-control" id="province" name="province" placeholder="Province" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                        </div>
                    </form>
                </div>
                <button id="saveEdit" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;">Save Changes</button>
            </div>
        </div>
    </div>

    <!--Create product modal -->
    <div class="modal" id="createProductModal">
        <div class="modal-dialog">
            <div class="modal-content create">
                <div class="modal-header">
                    <h5>New Address</h5>
                    <div>
                        <button type="button" class="close-modal" data-dismiss="modal">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error) : ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fullName">Full Name:</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phoneNumber">Phone No:</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="houseNo">House No:</label>
                                <input type="text" class="form-control" id="houseNo" name="houseNo" placeholder="House No">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="zipCode">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" placeholder="Zip Code" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="street">Street:</label>
                                <input type="text" class="form-control" id="street" name="street" placeholder="Street" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="barangay">Barangay:</label>
                                <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="city">City/ Municipality:</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City/ Municipality" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="province">Province:</label>
                                <input type="text" class="form-control" id="province" name="province" placeholder="Province" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="AddedAt" name="AddedAt" value="<?php echo (date("Y-m-d H:i:s")); ?>">
                            <input type="hidden" class="form-control" id="UpdatedAt" name="UpdatedAt" value="<?php echo (date("Y-m-d H:i:s")); ?>">
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="isDefault" name="isDefault">
                            <label class="form-check-label" for="isDefault">Set as Default Address</label>
                        </div>
                        <button class="btn-submit w-100" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Popup -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="color: tomato;">Delete</h5>
                    <div class="right-content">
                        <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
                    </div>
                </div>
                <div class="content">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" id="deleteAddressId" name="addressID">
                        <p>Are you sure you want to delete this address?</p>
                        <input type="submit" name="delete" value="Delete Address" style="background-color: DodgerBlue; color: #fff; padding: 8px 15px; border: none;">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- // Function to open modal with product data -->
    <script>
        function openModal(productId, productName, description, price, quantityAvailable) {
            document.getElementById("productId").value = productId;
            document.getElementById("productName").value = productName;
            document.getElementById("description").value = description;
            document.getElementById("price").value = price;
            document.getElementById("quantityAvailable").value = quantityAvailable;
            modal.style.display = "block";
        }

        // Function to close modal
        function closeModal() {
            modal.style.display = "none";
        }

        // Function to open the create address modal
        function openCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "block";
        }

        // Function to close the create address modal
        function closeCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "none";
        }

        // Function to open the create address modal
        function openUpdateModal() {
            var editForm = document.getElementById("editForm");
            editForm.style.display = "block";
        }

        // Function to open delete modal with product ID
        function openDeleteModal(addressId) {
            document.getElementById("deleteAddressId").value = addressId;
            deleteModal.style.display = "block";
        }

        // Function to close delete modal
        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        // Function to close the create address modal
        function closeUpdateModal() {
            var editForm = document.getElementById("editForm");
            editForm.style.display = "none";
        }

        // Close the modal when user clicks outside of it
        window.onclick = function(event) {
            if (event.target == updateModal) {
                updateModal.style.display = "none";
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }
    </script>

    <script>
        // Function to populate edit form with address details
        // Function to populate edit form with address details
        // Function to populate edit form with address details
        function populateEditForm(addressData) {
            var editForm = document.getElementById("editAddressForm");
            editForm.innerHTML = `
        <input type="hidden" name="addressID" value="${addressData.AddressID}">
        <input type="hidden" name="addressID" value="${addressData.AddressID}">

 <div class="form-row">
                            <div class="form-group col-md-6">
                <label for="fullName" style="font-size: 10px;">Full Name:</label>
                <input type="text" name="fullName" value="${addressData.FullName}" placeholder="Full Name" required title="Full Name" style="width: calc(100% - 5px);">
            </div>
            <div class="form-group col-md-6">
                <label for="phoneNumber" style="font-size: 10px;">Phone No:</label>
                <input type="text" name="phoneNumber" value="${addressData.PhoneNumber}" placeholder="Phone Number" required title="Phone Number" style="width: calc(100% - 5px);">
            </div>
        </div>

 <div class="form-row">
                            <div class="form-group col-md-6">
                <label for="houseNo" style="font-size: 10px;">House No:</label>
                <input type="text" name="houseNo" value="${addressData.HouseNo}" placeholder="House No" required title="House No." style="width: calc(100% - 5px);">
            </div>
            <div class="form-group col-md-6">
                <label for="zipCode" style="font-size: 10px;">Zip Code:</label>
                <input type="text" name="zipCode" value="${addressData.ZipCode}" placeholder="Zip Code" required title="Zip Code" style="width: calc(100% - 5px);">
            </div>
        </div>

         <div class="form-row">
            <div class="form-group col-md-6">
                <label for="street" style="font-size: 10px;">Street:</label>
                <input type="text" name="street" value="${addressData.Street}" placeholder="Street" required title="Street" style="width: calc(100% - 5px);">
            </div>
            <div class="form-group col-md-6">
                <label for="barangay" style="font-size: 10px;">Barangay:</label>
                <input type="text" name="barangay" value="${addressData.Barangay}" placeholder="Barangay" required title="Barangay" style="width: calc(100% - 5px);">
            </div>
        </div>

 <div class="form-row">
                            <div class="form-group col-md-6">
                <label for="city" style="font-size: 10px;">City/ Municipality:</label>
                <input type="text" name="city" value="${addressData.City}" placeholder="City/ Municipality" required title="City" style="width: calc(100% - 5px);">
            </div>
            <div class="form-group col-md-6">
                <label for="province" style="font-size: 10px;">Province:</label>
                <input type="text" name="province" value="${addressData.Province}" placeholder="Province" required title="Province" style="width: calc(100% - 5px);">
            </div>
        </div>

        <label for="description" style="font-size: 10px; margin-top:10px">Description:</label>
        <input type="text" name="description" value="${addressData.Description}" placeholder="Description" required title="Description"><br>
        `;
        }

        // Get all edit buttons
        var editButtons = document.querySelectorAll('.edit-address');

        // Loop through each edit button and add click event listener
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var addressData = JSON.parse(this.getAttribute('data-address'));
                populateEditForm(addressData);
                document.getElementById("editForm").style.display = "block";
            });
        });

        // Close the edit form when the close button is clicked
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById("editForm").style.display = "none";
        });

        // Handle save changes button click event
        document.getElementById('saveEdit').addEventListener('click', function() {
            // Prepare form data
            var formData = new FormData(document.getElementById('editAddressForm'));

            // Create XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure AJAX request
            xhr.open('POST', 'address/edit_address.php', true);

            // Define what happens on successful data submission
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle success
                    document.getElementById("editForm").style.display = "none";
                    // Reload the page or update the address list as needed
                    location.reload(); // Example: reload the page
                } else {
                    // Handle error
                    console.error('Error while updating address:', xhr.statusText);
                }
            };

            // Define what happens in case of error
            xhr.onerror = function() {
                console.error('AJAX request failed.');
            };

            // Send form data
            xhr.send(formData);
        });
    </script>
</body>

</html>