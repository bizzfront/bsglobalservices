<?php
$allowedOrigin = 'https://bsglobalservices.com';
$allowedIps = ['127.0.0.1']; // Update with trusted IPs

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$ip     = $_SERVER['REMOTE_ADDR'] ?? '';

if ($origin !== $allowedOrigin && !in_array($ip, $allowedIps, true)) {
    $token      = $_POST['token'] ?? '';
    $validToken = getenv('LEAD_FORM_TOKEN');

    if (!$validToken || !hash_equals($validToken, $token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}

header("Access-Control-Allow-Origin: $allowedOrigin");
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Vary: Origin');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Retrieve form fields if needed
$name    = $_POST['name'] ?? '';
$phone   = $_POST['phone'] ?? '';
$email   = $_POST['email'] ?? '';
$service = $_POST['service'] ?? '';
$message = $_POST['message'] ?? '';

header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
?>
