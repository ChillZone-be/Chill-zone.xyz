<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Empfange Skrill Callback Daten
$data = $_POST;

// Logge den Callback
logPaymentEvent('skrill_callback', $data);

// Validiere die Signatur
if (!validateSkrillPayment($data)) {
    http_response_code(400);
    exit('Invalid signature');
}

// Verarbeite den Zahlungsstatus
$orderId = substr($data['transaction_id'], 6); // Entferne 'order_' prefix
$status = $data['status'];

switch ($status) {
    case '2': // Verarbeitet
        updateOrderStatus($pdo, $orderId, 'completed');
        break;
    case '0': // Ausstehend
        updateOrderStatus($pdo, $orderId, 'pending');
        break;
    case '-2': // Abgelehnt
        updateOrderStatus($pdo, $orderId, 'failed');
        break;
    default:
        updateOrderStatus($pdo, $orderId, 'unknown');
}

// Bestätige den Empfang
http_response_code(200);
echo 'OK';
