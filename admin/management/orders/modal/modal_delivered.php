<script>
    // Select All Checkbox functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.order-checkbox');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    // Clear individual checkboxes when the clear button is clicked
    document.getElementById('clearCheckboxes').addEventListener('click', function() {
        var checkboxes = document.querySelectorAll('.order-checkbox');
        for (var checkbox of checkboxes) {
            checkbox.checked = false;
        }
        document.getElementById('selectAllCheckbox').checked = false;
    });
</script>

<!-- Global Edit Modal -->
<div id="globalEditModal" class="modal">
    <div class="modal-content">
        <h4>Update Status</h4>
        <form method="post" id="globalEditForm">
            <div class="form-group">
                <label for="newGlobalOrderStatus">Order Status</label>
                <select name="new_global_order_status" id="newGlobalOrderStatus" class="form-select form-select-sm">
                    <option value="" disabled selected>Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>

            <div class="form-group">
                <label for="newGlobalPaymentStatus">Payment Status</label>
                <select name="new_global_payment_status" id="newGlobalPaymentStatus" class="form-select form-select-sm">
                    <option value="" disabled selected>Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <!-- This hidden input will store selected order IDs -->
            <input type="hidden" name="selected_order_ids" id="selectedOrderIds">

            <div class="action-btn d-flex justify-content-end">
                <button type="button" class="btn btn-outline-basic" onclick="closeGlobalEditModal()">Cancel</button>
                <button type="submit" name="update_global_status" class="btn btn-primary">Update</button>
            </div>

        </form>
    </div>
</div>

<!-- Modal HTML -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Scan this QR Code to mark order as Delivered</p>
        <div id="qrCodeContainer"></div>
        <a id="downloadLink" download="QRCode.png">Download QR Code</a>
    </div>
</div>
<!-- Modal HTML for Shipping Address -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close btn btn-outline-danger ms-auto rounded-0" onclick="closeModal()">&times;</span>
        <p id="shippingAddressContent"></p>
    </div>
</div>

<!-- outside of the section -->
<script>
    // Function to clear all selected checkboxes
    function clearSelectedCheckboxes() {
        document.querySelectorAll('.order-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    }

    // Attach the function to the "Clear" button
    document.getElementById('clearCheckboxes').addEventListener('click', clearSelectedCheckboxes);
</script>
<!-- // Open the global edit modal -->
<script>
    function openGlobalEditModal() {
        var modal = document.getElementById("globalEditModal");
        var selectedOrderIds = [];

        // Get all checked checkboxes and store their values (Order IDs)
        document.querySelectorAll('.order-checkbox:checked').forEach(function(checkbox) {
            selectedOrderIds.push(checkbox.value);
        });

        // Check if there are any selected orders
        if (selectedOrderIds.length === 0) {
            alert("Please select at least one order.");
            return;
        }

        // Set the hidden input value with the selected Order IDs
        document.getElementById("selectedOrderIds").value = selectedOrderIds.join(",");

        // Show the modal
        modal.style.display = "block";
    }

    // Close the modal
    function closeGlobalEditModal() {
        var modal = document.getElementById("globalEditModal");
        modal.style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        var modal = document.getElementById("globalEditModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- qr -->
<script>
    // Function to display modal with shipping address
    function displayShippingAddress(address) {
        var modal = document.getElementById("myModal");
        var addressContent = document.getElementById("shippingAddressContent");
        addressContent.innerHTML = "<strong>Shipping Address:</br></strong> " + address;
        modal.style.display = "block";
    }

    // Function to generate QR code and display in modal
    function generateQRCode(orderID) {
        fetch('generate_qr.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'generate_qr=true&order_id=' + orderID,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var qrModal = document.getElementById("qrModal");
                    var qrCodeContainer = document.getElementById("qrCodeContainer");
                    qrCodeContainer.innerHTML = '<img src="' + data.qrImage + '" alt="QR Code">';
                    var downloadLink = document.getElementById("downloadLink");
                    downloadLink.href = data.qrImage;
                    qrModal.style.display = "block";
                } else {
                    alert("Error generating QR code.");
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function closeModal() {
        var qrModal = document.getElementById("qrModal");
        qrModal.style.display = "none";
        var myModal = document.getElementById("myModal");
        myModal.style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        var qrModal = document.getElementById("qrModal");
        var myModal = document.getElementById("myModal");
        if (event.target == qrModal || event.target == myModal) {
            qrModal.style.display = "none";
            myModal.style.display = "none";
        }
    }
</script>