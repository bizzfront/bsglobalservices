<?php
header('Content-Type: application/json');

// Database connection
$mysqli = new mysqli(
    getenv('DB_HOST') ?: 'localhost',
    getenv('DB_USER') ?: 'user',
    getenv('DB_PASS') ?: 'password',
    getenv('DB_NAME') ?: 'database'
);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'code' => 500,
        'data' => 'Database connection failed: ' . $mysqli->connect_error
    ]);
    exit;
}

// Collect input values
$name    = $_POST['name']    ?? '';
$phone   = $_POST['phone']   ?? '';
$email   = $_POST['email']   ?? '';
$service = $_POST['service'] ?? '';
$message = $_POST['message'] ?? '';
$form    = $_POST['form_name'] ?? '';
$source  = $_POST['source']  ?? '';

$stmt = $mysqli->prepare('INSERT INTO leads (name, phone, email, service, message, form_name, source) VALUES (?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'code' => 500,
        'data' => 'Statement prepare failed: ' . $mysqli->error
    ]);
    $mysqli->close();
    exit;
}

$stmt->bind_param('sssssss', $name, $phone, $email, $service, $message, $form, $source);

if ($stmt->execute()) {
    echo json_encode([
        'code' => 200,
        'data' => 'Lead stored successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'code' => 500,
        'data' => 'Execution failed: ' . $stmt->error
    ]);
}

$stmt->close();
$mysqli->close();
?>
