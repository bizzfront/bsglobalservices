<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$name    = $_POST['name'] ?? '';
$phone   = $_POST['phone'] ?? '';
$email   = $_POST['email'] ?? '';
$service = $_POST['service'] ?? '';
$message = $_POST['message'] ?? '';
$formName = $_POST['form_name'] ?? 'Lead';

$subject = 'New lead from B&S website';
$subjectCopy = 'Copy of your request to B&S Floor Supply';

ob_start();
include __DIR__ . '/email-template.php';
$body = ob_get_clean();

function sendMailWithHeaders($to, $subject, $body, $replyTo)
{
    $headers = "From: info@bsglobalservices.com\r\n";
    if (!empty($replyTo)) {
        $headers .= "Reply-To: $replyTo\r\n";
    }
    $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $body, $headers);
}

function sendCopy($to, $subjectCopy, $body)
{
    return sendMailWithHeaders($to, $subjectCopy, $body, 'info@bsglobalservices.com');
}

$sent = sendMailWithHeaders('info@bsglobalservices.com', $subject, $body, $email);

if ($sent && !empty($email)) {
    sendCopy($email, $subjectCopy, $body);
}

echo $sent ? 'OK' : 'ERROR';
