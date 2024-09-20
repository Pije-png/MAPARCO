function domReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1000);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

domReady(function () {
    function onScanSuccess(decodeText, decodeResult) {
        // Display the decoded text (order ID) on the page
        alert("Scanned QR Code content: " + decodeText);
        
        // Send the scanned QR code data to the backend
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + encodeURIComponent(decodeText),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product status updated to delivered');
            } else {
                alert('Failed to update product status: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    let htmlscanner = new Html5QrcodeScanner(
        "my-qr-reader",
        { fps: 10, qrbox: 250 }
    );
    htmlscanner.render(onScanSuccess);
});
