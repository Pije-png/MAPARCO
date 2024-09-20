var modal = document.getElementById("productModal");
var buttons = document.querySelectorAll(".view-button");
var span = document.getElementsByClassName("close")[0];

// Function to display modal with product information
function showModal(product) {
    var productInfo = document.getElementById("productInfo");
    productInfo.innerHTML = "<h2>" + product.ProductName + "</h2>" +
        "<img src='admin/management/" + product.Photo + "' alt='Product Image' class='product-image'><br>" +
        "<p><strong>Description:</strong> " + product.Description + "</p>" +
        "<p><strong>Price:</strong> $" + product.Price + "</p>";
    modal.style.display = "block";
}

// Event listener for view buttons
buttons.forEach(function (button) {
    button.addEventListener("click", function () {
        var productData = JSON.parse(this.getAttribute("data-product"));
        showModal(productData);
    });
});

// Close the modal when the close button is clicked
span.onclick = function () {
    modal.style.display = "none";
}

// Close the modal when the user clicks anywhere outside of the modal
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}