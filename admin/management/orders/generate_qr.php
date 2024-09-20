<?php
require '../../../vendor/autoload.php'; // Ensure you have composer autoload file included
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

include '../../../connection.php';

header('Content-Type: application/json');

if (isset($_POST['generate_qr']) && isset($_POST['order_id'])) {
    $orderID = intval($_POST['order_id']);

    $options = new QROptions([
        'eccLevel' => QRCode::ECC_L,
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'imageBase64' => true,
    ]);

    $qrcode = new QRCode($options);
    $qrImage = $qrcode->render($orderID);

    echo json_encode([
        'success' => true,
        'qrImage' => $qrImage,
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request',
    ]);
}
?>
