
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection file
include '../../connection.php';

// Check if the request is an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Fetch messages list from the database
    $sql = "SELECT m.customer_id, c.Name, c.ProfilePicFilename, MAX(m.timestamp) AS last_timestamp, m.message_text AS last_message 
            FROM messages m
            JOIN customers c ON m.customer_id = c.CustomerID
            GROUP BY m.customer_id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $messagesList = array();
        while ($row = $result->fetch_assoc()) {
            $messagesList[] = array(
                'customer_id' => $row['customer_id'],
                'Name' => $row['Name'],
                'ProfilePicFilename' => $row['ProfilePicFilename'],
                'last_timestamp' => $row['last_timestamp'],
                'last_message' => $row['last_message']
            );
        }
        // Convert the array to JSON format and echo the response
        echo json_encode($messagesList);
    } else {
        // No messages found
        echo json_encode(array('error' => 'No messages found'));
    }
} else {
    // Handle invalid requests
    http_response_code(403); // Forbidden
    echo json_encode(array('error' => 'Invalid request'));
}
?>
<?php include 'sidebar.php'; ?>
    <?php include 'navbar.php'; ?>