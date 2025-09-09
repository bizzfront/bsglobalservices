<?php
header('Content-Type: application/json');

$expected = ['name','phone','email','service','message','form_name','source'];
$lead = [];
foreach ($expected as $field) {
    $lead[$field] = isset($_POST[$field]) ? trim((string)$_POST[$field]) : '';
}

$formName = $lead['form_name'];
$subject = $formName ? "New lead from {$formName}" : 'New lead';

$bodyLines = ["Lead details:" ];
foreach ($lead as $key => $value) {
    $bodyLines[] = ucfirst($key) . ': ' . $value;
}
$body = implode("\n", $bodyLines);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bs_global', 'user', 'password', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $stmt = $pdo->prepare('INSERT INTO leads (name, phone, email, service, message, form_name, source) VALUES (:name, :phone, :email, :service, :message, :form_name, :source)');
    $stmt->execute($lead);

    $mailSent = mail('info@bsglobalservices.com', $subject, $body);
    if (!$mailSent) {
        echo json_encode(['code' => 500, 'data' => 'Email sending failed']);
        exit;
    }

    echo json_encode(['code' => 200, 'data' => 'Lead stored and email sent']);
} catch (Exception $e) {
    echo json_encode(['code' => 500, 'data' => $e->getMessage()]);
}
