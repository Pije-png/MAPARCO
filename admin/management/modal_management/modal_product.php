    <!--Create product modal -->
    <div id="createProductModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="content">
                    <div class="modal-header row">
                        <h3 class="text-danger col-11">Product Information</h3>
                        <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeCreateModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                            <!-- Your form fields go here -->
                            <label for="productName">Product Name:</label><br>
                            <input type="text" name="productName" placeholder="Product Name" required>
                            <label for="description">Description:</label><br>
                            <input type="text" name="description" placeholder="Description" required>
                            <label for="price">Price:</label><br>
                            <input type="text" name="price" placeholder="Price" required>
                            <label for="quantityAvailable">quantityAvailable:</label><br>
                            <input type="text" name="quantityAvailable" placeholder="Quantity Available" required>
                            <label for="photo">Upload Photo:</label><br>
                            <input type="file" name="photo" required>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" name="submit" value="Add Product">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Update Modal Popup -->
    <div id="updateModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="content">
                    <div class="modal-header row">
                        <h3 class="text-danger col-11">Update Information</h3>
                        <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" id="productId" name="productId">
                            <label for="productName">Product Name:</label><br>
                            <input type="text" id="productName" name="productName" required><br>
                            <label for="description">Description:</label><br>
                            <textarea id="description" name="description" required></textarea><br>
                            <label for="price">Price:</label><br>
                            <input type="text" id="price" name="price" required><br>
                            <label for="quantityAvailable">Quantity Available:</label><br>
                            <input type="text" id="quantityAvailable" name="quantityAvailable" required><br>
                            <input type="submit" name="update" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Popup -->
    <div id="deleteModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="content">
                    <div class="modal-header row">
                        <h3 class="text-danger col-11">Delete</h3>
                        <span class="close-modal btn btn-outline-danger rounded-0 col" onclick="closeDeleteModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" id="deleteProductId" name="productID">
                            <p>Are you sure you want to delete this product?</p>
                            <input type="submit" name="delete" value="Delete Product">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("updateModal");
        var deleteModal = document.getElementById("deleteModal");

        // Function to open modal with product data
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

        // Function to open delete modal with product ID
        function openDeleteModal(productId) {
            document.getElementById("deleteProductId").value = productId;
            deleteModal.style.display = "block";
        }

        // Function to close delete modal
        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        // Function to open the create product modal
        function openCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "block";
        }

        // Function to close the create product modal
        function closeCreateModal() {
            var createProductModal = document.getElementById("createProductModal");
            createProductModal.style.display = "none";
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