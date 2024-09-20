<?php
include '../../connection.php';
// Fetch out-of-stock products
$out_of_stock_products = array();
$sql_out_of_stock = "SELECT ProductName, Photo FROM products WHERE QuantityAvailable = 0";
$result_out_of_stock = $conn->query($sql_out_of_stock);
if ($result_out_of_stock->num_rows > 0) {
    while ($row = $result_out_of_stock->fetch_assoc()) {
        $out_of_stock_products[] = $row;

        // Send SMS to the user with phone number 09270378521
        $phoneNumber = '09270378521'; // Change to actual phone number
        $message = "The product '{$row['ProductName']}' is out of stock.";
        sendSMS($phoneNumber, $message);
    }
}

$conn->close();

// Function to send SMS
function sendSMS($phoneNumber, $message)
{
    $apiKey = 'ef0a9cf7d5bf8f4b43bbdac91a2f1276'; // Replace 'YOUR_API_KEY' with your Semaphore API key

    $parameters = [
        'apikey' => $apiKey,
        'number' =>  $phoneNumber, // Replace with recipient's phone number
        'message' => $message,
        'sendername' => 'SEMAPHORE'
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disable SSL certificate verification (not recommended for production)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute cURL request
    $output = curl_exec($ch);

    // Close cURL resource
    curl_close($ch);

    // Check if the SMS was sent successfully
    if ($output !== false) {
        // SMS sent successfully
        echo "SMS sent successfully to $phoneNumber: $message\n";
    } else {
        // Failed to send SMS
        echo "Failed to send SMS to $phoneNumber: $message\n";
    }
}
?>
