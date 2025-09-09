<?php
// Basic lead handler

function valid_phone(string $phone): bool {
    return (bool)preg_match('/^\+?[0-9\s\-()]{7,20}$/', $phone);
}

function valid_zip(string $zip): bool {
    return (bool)preg_match('/^\d{5}(?:-\d{4})?$/', $zip);
}

$fields = ['name','phone','email','service','message','zip','form_name','source'];
$data = [];
foreach ($fields as $f) {
    $data[$f] = isset($_POST[$f]) ? substr(trim($_POST[$f]), 0, 255) : '';
}

if (!valid_phone($data['phone'])) {
    http_response_code(400);
    echo 'Invalid phone';
    exit;
}
if ($data['zip'] !== '' && !valid_zip($data['zip'])) {
    http_response_code(400);
    echo 'Invalid zip';
    exit;
}

$email   = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$subject = preg_replace("/[\r\n]+/", ' ', 'New lead from '.$data['form_name']);
$from    = preg_replace("/[\r\n]+/", '', $email);

$headers = [
    'From: '.$from,
    'Reply-To: '.$from
];

$body = "Name: {$data['name']}\n".
        "Phone: {$data['phone']}\n".
        "Email: {$email}\n".
        "Service: {$data['service']}\n".
        "ZIP: {$data['zip']}\n".
        "Message: {$data['message']}\n".
        "Source: {$data['source']}\n";

mail('info@globalservices.com', $subject, $body, implode("\r\n", $headers));

echo 'OK';
?>
