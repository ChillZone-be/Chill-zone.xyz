<?php
header('Content-Type: application/json');

// Basis-Sicherheitscheck
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Erstelle maintenance.enable
file_put_contents('../maintenance.enable', time() + 300); // Current time + 5 minutes

echo json_encode(['status' => 'success']); 